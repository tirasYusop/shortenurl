<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>URL Shortener</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="layout">
    <div class="welcome">
      <h1>Welcome to<br>URL Shortener</h1>
      <p class="subtitle">Created by: Nor Atira Binti Yusop</p>
    </div>
    <div class="card">
      <h1>URL Shortener</h1>
      <p>Paste a long link and get a short one.</p>

      <form action="shorten.php" method="POST">
        <input type="url" name="url" placeholder="https://example.com/long/link" required />
        <button type="submit">Shorten</button>
      </form>

      <?php if (isset($_GET["short"])): ?>
        <div class="result">
          <p>Short URL:</p>
          <input type="text" value="<?php echo htmlspecialchars($_GET["short"]); ?>" readonly />
        </div>
      <?php endif; ?>

      <?php if (isset($_GET["error"])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET["error"]); ?></p>
      <?php endif; ?>
    </div>

  </div>
</body>

</html>