<?php
session_start();
require_once __DIR__ . '/config.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Simple router
switch ($path) {
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
        require __DIR__ . '/proxy.php';
        break;
    default:
        require __DIR__ . '/homepage.php';
        break;
}
