<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

// Get the clean path without query string
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize: remove any trailing slash except root
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Route table
switch ($path) {
    case '':
    case '/':
        require __DIR__ . '/homepage.php';
        break;

    case '/login':
        require __DIR__ . '/login.php';
        break;

    case '/register':
        require __DIR__ . '/register.php';
        break;

    case '/logout':
        require __DIR__ . '/logout.php';
        break;

    case '/proxy.php':
    case '/proxy':
        require __DIR__ . '/proxy.php';
        break;

    // If someone tries to go directly to a tab, redirect to homepage with ?tab=
    default:
        // If it matches a tab name, redirect
        $tabName = ltrim($path, '/');
        if (preg_match('/^[a-z0-9_-]+$/i', $tabName)) {
            header('Location: /?tab=' . urlencode($tabName));
            exit;
        }

        // Otherwise, 404
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The page you requested does not exist.</p>";
        break;
}
