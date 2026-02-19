<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Admin - Wijzig product</title>
</head>
<body>
  <h1>Wijzig product</h1>

  <form method="post" action="<?= htmlspecialchars(base_url('/?r=admin/product/edit')) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$product['product_id'] ?>">

    <label>Naam<br>
      <input type="text" name="naam" value="<?= htmlspecialchars($product['naam']) ?>" required>
    </label><br><br>

    <label>Prijs (€)<br>
      <input type="number" step="0.01" name="prijs" value="<?= htmlspecialchars((string)$product['prijs']) ?>" required>
    </label><br><br>

    <label>Beschrijving<br>
      <textarea name="beschrijving" rows="4"><?= htmlspecialchars((string)($product['beschrijving'] ?? '')) ?></textarea>
    </label><br><br>

    <label>Categorie ID<br>
      <input type="number" name="categorie_id" value="<?= (int)($product['categorie_id'] ?? 1) ?>" min="1">
    </label><br><br>

    <button type="submit">Opslaan</button>
  </form>

  <p><a href="<?= htmlspecialchars(base_url('/?r=admin/products')) ?>">Terug</a></p>
</body>
</html>
