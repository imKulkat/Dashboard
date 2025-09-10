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
    $confirm  = (string)($_POST['confirm'] ?? '');

    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $errors[] = 'Username must be 3–50 characters (letters, numbers, underscore).';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        if (find_user($username)) {
            $errors[] = 'Username is already taken.';
        } else {
            upsert_user([
                'username'   => $username,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'role'       => 'user',
                'created_at' => date('c')
            ]);
            $_SESSION['username'] = $username;
            header('Location: /');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register - Dashboard</title>
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
      <input type="password" name="confirm" placeholder="Confirm Password" required>
      <button type="submit" class="btn">Register</button>
    </form>
    <p><a href="/login">Already have an account? Log in</a></p>
  </div>
</div>
</body>
</html>
