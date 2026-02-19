<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Parfums</title>
</head>
<body>
  <h1>Parfums</h1>

  <?php if (empty($products)): ?>
    <p>Geen parfums gevonden.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($products as $p): ?>
        <li>
          <?= htmlspecialchars($p['naam']) ?> - €<?= htmlspecialchars((string)$p['prijs']) ?>
          <a href="<?= htmlspecialchars(base_url('/?r=parfum&id=' . (int)$p['product_id'])) ?>">
            Bekijk
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <p>
    <a href="<?= htmlspecialchars(base_url('/')) ?>">Terug</a>
  </p>
  <p>
  <a href="<?= htmlspecialchars(base_url('/?r=cart')) ?>">Winkelmandje bekijken</a>
</p>

</body>
</html>
