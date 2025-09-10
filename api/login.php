<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

if (isset($_SESSION['username']) && find_user($_SESSION['username'])) {
    header('Location: /');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $user = find_user($username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header('Location: /');
        exit;
    } else {
        $errors[] = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/api/style.css">
</head>
<body>
<div class="auth-container">
  <div class="auth-box">
    <h1>Dashboard</h1>
    <?php foreach ($errors as $e): ?>
      <p class="error"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn">Login</button>
    </form>
    <p style="margin-top:10px;">Default owner: <code>owner / owner123</code></p>
    <p><a href="/register">Create an account</a></p>
  </div>
</div>
</body>
</html>
