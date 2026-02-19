<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/core/Router.php';
require_once __DIR__ . '/../src/core/View.php';
require_once __DIR__ . '/../src/core/Db.php';
require_once __DIR__ . '/../src/core/Session.php';
require_once __DIR__ . '/../src/core/Csrf.php';

require_once __DIR__ . '/../src/repositories/ProductRepository.php';
require_once __DIR__ . '/../src/services/CartService.php';

$config = require __DIR__ . '/../src/config/config.php';

Session::start();

$base = rtrim($config['app']['base_url'], '/');

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

        if (!isset($products[$id])) {
            continue;
        }

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

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
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

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
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

/* ROUTING VIA ?r= */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route  = $_GET['r'] ?? '/';
$route  = '/' . trim((string)$route, '/');

$router->dispatch($method, $route);
