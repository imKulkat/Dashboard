<?php
declare(strict_types=1);
if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '/config.php';

$user = find_user($_SESSION['username']) ?? ['username' => $_SESSION['username'], 'role' => 'user'];
$role = $user['role'] ?? 'user';

$tabs = [
    'user' => [
        'home'         => '🏠 Home',
        'qa'           => '❓ Q&A',
        'requests'     => '📄 Website Request',
        'classicgames' => '🎮 Classic Games',
        'eaglrcraft'   => '⛏️ EaglrCraft',
        'codzombies'   => '🧟 COD Zombies'
    ],
    'admin' => [
        'home'         => '🏠 Home',
        'qa'           => '❓ Q&A',
        'requests'     => '📄 Website Request',
        'classicgames' => '🎮 Classic Games',
        'eaglrcraft'   => '⛏️ EaglrCraft',
        'codzombies'   => '🧟 COD Zombies',
        'addicting'    => '🕹️ Addicting Games'
    ],
    'owner' => [
        'home'         => '🏠 Home',
        'qa'           => '❓ Q&A',
        'requests'     => '📄 Website Request',
        'classicgames' => '🎮 Classic Games',
        'eaglrcraft'   => '⛏️ EaglrCraft',
        'codzombies'   => '🧟 COD Zombies',
        'addicting'    => '🕹️ Addicting Games',
        'google'       => '🌐 Google',
        'webmin'       => '🖥️ Webmin',
        'chatgpt'      => '🤖 ChatGPT / Copilot',
        'analytics'    => '📊 Analytics'
    ]
];

$availableTabs = $tabs[$role] ?? $tabs['user'];
$currentTab = $_GET['tab'] ?? 'home';
$localTabs = ['qa','requests','analytics'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<link rel="stylesheet" href="/api/style.css">
</head>
<body>
<div class="sidebar">
  <div class="brand">Dashboard</div>
  <ul class="tab-list">
    <?php foreach ($availableTabs as $id => $label): ?>
      <li><a class="tab-link <?= $currentTab === $id ? 'active' : '' ?>" href="?tab=<?= urlencode($id) ?>"><?= $label ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
<div class="main">
  <div class="content">
    <?php
    if ($currentTab === 'home') {
        echo '<div class="panel"><h2>About Me</h2><p>Welcome to my dashboard!</p></div>';
    }
    elseif (in_array($currentTab, $localTabs, true)) {
        $file = TABS_DIR . "/{$currentTab}.php";
        if (file_exists($file)) include $file;
        else echo "<div class='panel'>Missing tab file: {$currentTab}.php</div>";
    }
    elseif (isset($availableTabs[$currentTab])) {
        echo '<iframe class="frame" src="/api/proxy.php?tab='.htmlspecialchars($currentTab).'"></iframe>';
    }
    ?>
  </div>
</div>
</body>
</html>
