<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Winkelmandje</title>
</head>
<body>
  <h1>Winkelmandje</h1>
  <p><strong>Aantal verschillende producten:</strong> <?= count($lines) ?></p>


  <?php if (empty($lines)): ?>
    <p>Je winkelmandje is leeg.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>Product</th>
          <th>Prijs</th>
          <th>Aantal</th>
          <th>Subtotaal</th>
          <th>Actie</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lines as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['naam']) ?></td>
            <td>€<?= number_format((float)$l['prijs'], 2) ?></td>
            <td>
  <form method="post" action="<?= htmlspecialchars(base_url('/?r=cart/update')) ?>">
    <input type="hidden" name="id" value="<?= (int)$l['product_id'] ?>">
    <input type="number" name="qty" value="<?= (int)$l['qty'] ?>" min="0">
    <button type="submit">Update</button>
  </form>
</td>
            <td>€<?= number_format((float)$l['line_total'], 2) ?></td>
            <td>
              <form method="post" action="<?= htmlspecialchars(base_url('/?r=cart/remove')) ?>">
                <input type="hidden" name="id" value="<?= (int)$l['product_id'] ?>">
                <button type="submit">Verwijderen</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p><strong>Totaal:</strong> €<?= number_format((float)$total, 2) ?></p>

    <form method="post" action="<?= htmlspecialchars(base_url('/?r=cart/clear')) ?>">
      <button type="submit">Leeg winkelmandje</button>
    </form>
  <?php endif; ?>

  <p>
    <a href="<?= htmlspecialchars(base_url('/?r=parfums')) ?>">Verder winkelen</a>
  </p>
</body>
</html>
