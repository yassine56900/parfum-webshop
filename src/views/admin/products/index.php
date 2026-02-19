<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Admin - Producten</title>
</head>
<body>
  <h1>Admin - Producten</h1>

  <p>
    <a href="<?= htmlspecialchars(base_url('/?r=admin/product/create')) ?>">+ Nieuw product</a>
  </p>

  <form method="post" action="<?= htmlspecialchars(base_url('/?r=admin/logout')) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <button type="submit">Uitloggen</button>
  </form>

  <table border="1" cellpadding="6">
    <thead>
      <tr>
        <th>ID</th>
        <th>Naam</th>
        <th>Prijs</th>
        <th>Acties</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= (int)$p['product_id'] ?></td>
          <td><?= htmlspecialchars($p['naam']) ?></td>
          <td>€<?= number_format((float)$p['prijs'], 2) ?></td>
          <td>
            <a href="<?= htmlspecialchars(base_url('/?r=admin/product/edit&id=' . (int)$p['product_id'])) ?>">Wijzigen</a>

            <form method="post" action="<?= htmlspecialchars(base_url('/?r=admin/product/delete')) ?>" style="display:inline;">
              <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$p['product_id'] ?>">
              <button type="submit" onclick="return confirm('Weet je het zeker?')">Verwijderen</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p><a href="<?= htmlspecialchars(base_url('/?r=parfums')) ?>">Naar webshop</a></p>
</body>
</html>
