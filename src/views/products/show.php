<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($product['naam']) ?></title>
</head>
<body>
  <h1><?= htmlspecialchars($product['naam']) ?></h1>

  <p><strong>Prijs:</strong> €<?= htmlspecialchars((string)$product['prijs']) ?></p>

  <p><strong>Beschrijving:</strong><br>
    <?= nl2br(htmlspecialchars((string)($product['beschrijving'] ?? ''))) ?>
  </p>

  <form method="post" action="<?= htmlspecialchars(base_url('/?r=cart/add')) ?>">
    <input type="hidden" name="id" value="<?= (int)$product['product_id'] ?>">
    <label>
      Aantal:
      <input type="number" name="qty" value="1" min="1">
    </label>
    <button type="submit">Toevoegen aan winkelmandje</button>
  </form>

  <p>
    <a href="<?= htmlspecialchars(base_url('/?r=cart')) ?>">Ga naar winkelmandje</a>
  </p>

  <p>
    <a href="<?= htmlspecialchars(base_url('/?r=parfums')) ?>">Terug</a>
  </p>
</body>
</html>
