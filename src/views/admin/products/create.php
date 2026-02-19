<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Admin - Nieuw product</title>
</head>
<body>
  <h1>Nieuw product</h1>

  <form method="post" action="<?= htmlspecialchars(base_url('/?r=admin/product/create')) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <label>Naam<br>
      <input type="text" name="naam" required>
    </label><br><br>

    <label>Prijs (€)<br>
      <input type="number" step="0.01" name="prijs" required>
    </label><br><br>

    <label>Beschrijving<br>
      <textarea name="beschrijving" rows="4"></textarea>
    </label><br><br>

    <label>Categorie ID<br>
      <input type="number" name="categorie_id" value="1" min="1">
    </label><br><br>

    <button type="submit">Opslaan</button>
  </form>

  <p><a href="<?= htmlspecialchars(base_url('/?r=admin/products')) ?>">Terug</a></p>
</body>
</html>
