<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Admin login</title>
</head>
<body>
  <h1>Admin login</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="<?= htmlspecialchars(base_url('/?r=admin/login')) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <label>Gebruikersnaam<br>
      <input type="text" name="username" required>
    </label><br><br>

    <label>Wachtwoord<br>
      <input type="password" name="password" required>
    </label><br><br>

    <button type="submit">Inloggen</button>
  </form>

  <p><a href="<?= htmlspecialchars(base_url('/?r=parfums')) ?>">Terug naar webshop</a></p>
</body>
</html>
