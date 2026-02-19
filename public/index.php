<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/core/Router.php';
require_once __DIR__ . '/../src/core/View.php';
require_once __DIR__ . '/../src/core/Db.php';
require_once __DIR__ . '/../src/core/Session.php';
require_once __DIR__ . '/../src/core/Csrf.php';

require_once __DIR__ . '/../src/repositories/ProductRepository.php';
require_once __DIR__ . '/../src/services/CartService.php';
require_once __DIR__ . '/../src/services/AdminAuthService.php';

$config = require __DIR__ . '/../src/config/config.php';

Session::start();

$base = rtrim((string)($config['app']['base_url'] ?? ''), '/');

function base_url(string $path = ''): string
{
    global $base;
    $path = '/' . ltrim($path, '/');
    return $base . ($path === '/' ? '' : $path);
}

$router = new Router();

/* HOME */
$router->get('/', function () {
    View::render('home.php');
});

/* PRODUCT OVERZICHT */
$router->get('/parfums', function () use ($config) {
    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $products = $repo->all();

    View::render('products/index.php', ['products' => $products]);
});

/* PRODUCT DETAIL */
$router->get('/parfum', function () use ($config) {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $product = $repo->findById((int)$id);

    if (!$product) {
        http_response_code(404);
        exit("Product niet gevonden.");
    }

    View::render('products/show.php', ['product' => $product]);
});

/* CART PAGINA */
$router->get('/cart', function () use ($config) {
    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $cart = new CartService();

    $items = $cart->items(); // productId => qty
    $products = $repo->findManyByIds(array_map('intval', array_keys($items)));

    $lines = [];
    $total = 0.0;

    foreach ($items as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;

        if (!isset($products[$id])) continue;

        $price = (float)$products[$id]['prijs'];
        $lineTotal = $price * $qty;
        $total += $lineTotal;

        $lines[] = [
            'product_id' => $id,
            'naam' => (string)$products[$id]['naam'],
            'prijs' => $price,
            'qty' => $qty,
            'line_total' => $lineTotal,
        ];
    }

    View::render('cart/index.php', [
        'lines' => $lines,
        'total' => $total
    ]);
});

/* ADD TO CART */
$router->post('/cart/add', function () {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $id  = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $qty = filter_var($_POST['qty'] ?? 1, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $cart = new CartService();
    $cart->add((int)$id, max(1, (int)$qty));

    header('Location: ' . base_url('/?r=cart'));
    exit;
});

/* UPDATE QUANTITY */
$router->post('/cart/update', function () {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $id  = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $qty = filter_var($_POST['qty'] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1 || $qty === false) {
        http_response_code(400);
        exit("Ongeldige invoer.");
    }

    $cart = new CartService();
    $cart->update((int)$id, (int)$qty);

    header('Location: ' . base_url('/?r=cart'));
    exit;
});

/* REMOVE */
$router->post('/cart/remove', function () {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $cart = new CartService();
    $cart->remove((int)$id);

    header('Location: ' . base_url('/?r=cart'));
    exit;
});

/* CLEAR */
$router->post('/cart/clear', function () {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $cart = new CartService();
    $cart->clear();

    header('Location: ' . base_url('/?r=cart'));
    exit;
});

/* =========================
   ADMIN (FE11/FE12)
   ========================= */

/* ADMIN LOGIN (GET) */
$router->get('/admin/login', function () {
    View::render('admin/login.php');
});

/* ADMIN LOGIN (POST) */
$router->post('/admin/login', function () use ($config) {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (AdminAuthService::login($config, $username, $password)) {
        header('Location: ' . base_url('/?r=admin/products'));
        exit;
    }

    View::render('admin/login.php', ['error' => 'Onjuiste inloggegevens.']);
});

/* ADMIN LOGOUT */
$router->post('/admin/logout', function () {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    AdminAuthService::logout();
    header('Location: ' . base_url('/?r=admin/login'));
    exit;
});

/* ADMIN PRODUCTS LIST */
$router->get('/admin/products', function () use ($config) {
    AdminAuthService::requireAdmin();

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $products = $repo->all();

    View::render('admin/products/index.php', ['products' => $products]);
});

/* ADMIN CREATE (GET) */
$router->get('/admin/product/create', function () {
    AdminAuthService::requireAdmin();
    View::render('admin/products/create.php');
});

/* ADMIN CREATE (POST) */
$router->post('/admin/product/create', function () use ($config) {
    AdminAuthService::requireAdmin();

    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $naam = trim((string)($_POST['naam'] ?? ''));
    $prijs = (float)($_POST['prijs'] ?? 0);
    $beschrijving = trim((string)($_POST['beschrijving'] ?? ''));
    $categorie_id = (int)($_POST['categorie_id'] ?? 1);

    if ($naam === '' || $prijs <= 0) {
        http_response_code(400);
        exit("Naam en prijs zijn verplicht.");
    }

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $repo->create([
        'naam' => $naam,
        'prijs' => $prijs,
        'beschrijving' => $beschrijving,
        'categorie_id' => $categorie_id
    ]);

    header('Location: ' . base_url('/?r=admin/products'));
    exit;
});

/* ADMIN EDIT (GET) */
$router->get('/admin/product/edit', function () use ($config) {
    AdminAuthService::requireAdmin();

    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $product = $repo->findById((int)$id);

    if (!$product) {
        http_response_code(404);
        exit("Product niet gevonden.");
    }

    View::render('admin/products/edit.php', ['product' => $product]);
});

/* ADMIN EDIT (POST) */
$router->post('/admin/product/edit', function () use ($config) {
    AdminAuthService::requireAdmin();

    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $naam = trim((string)($_POST['naam'] ?? ''));
    $prijs = (float)($_POST['prijs'] ?? 0);
    $beschrijving = trim((string)($_POST['beschrijving'] ?? ''));
    $categorie_id = (int)($_POST['categorie_id'] ?? 1);

    if ($naam === '' || $prijs <= 0) {
        http_response_code(400);
        exit("Naam en prijs zijn verplicht.");
    }

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $repo->update((int)$id, [
        'naam' => $naam,
        'prijs' => $prijs,
        'beschrijving' => $beschrijving,
        'categorie_id' => $categorie_id
    ]);

    header('Location: ' . base_url('/?r=admin/products'));
    exit;
});

/* ADMIN DELETE (POST) */
$router->post('/admin/product/delete', function () use ($config) {
    AdminAuthService::requireAdmin();

    if (!Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit("Ongeldige CSRF token.");
    }

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Ongeldig product.");
    }

    $pdo = Db::get($config['db']);
    $repo = new ProductRepository($pdo);
    $repo->delete((int)$id);

    header('Location: ' . base_url('/?r=admin/products'));
    exit;
});

/* ROUTING VIA ?r= */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route  = $_GET['r'] ?? '/';
$route  = '/' . trim((string)$route, '/');

try {
    $router->dispatch($method, $route);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Er is iets misgegaan. Probeer later opnieuw.";
}
