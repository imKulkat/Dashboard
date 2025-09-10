<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}

$username = $_SESSION['username'];
$user = find_user($username);

if (!$user) {
    session_destroy();
    header('Location: /login');
    exit;
}

$role = $user['role'] ?? 'user';
$TABS = require __DIR__ . '/tabs_config.php';
$tabsForRole = $TABS[$role] ?? $TABS['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        nav { background: #333; padding: 10px; }
        nav a { color: white; margin-right: 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        iframe { width: 100%; height: 80vh; border: none; }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($username) ?> (Role: <?= htmlspecialchars($role) ?>)</h1>
    <nav>
        <?php foreach ($tabsForRole as $label => $url): ?>
            <a href="<?= htmlspecialchars($url) ?>" target="contentFrame"><?= htmlspecialchars($label) ?></a>
        <?php endforeach; ?>
        <a href="/logout">Logout</a>
    </nav>
    <iframe name="contentFrame" src="<?= htmlspecialchars(reset($tabsForRole)) ?>"></iframe>
</body>
</html>
