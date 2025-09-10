<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Not logged in');
}

$user = find_user($_SESSION['username']) ?? ['role' => 'user'];
$role = $user['role'] ?? 'user';

// Only proxied tabs go here. Local tabs (qa, requests, analytics) are included by homepage.php.
$TAB_MAP = [
    'user' => [
        'classicgames' => 'https://playclassic.games/',
        'eaglrcraft'   => 'https://eaglercraft.com/mc/1.8.8',
        'codzombies'   => 'https://nzp.gay/'
    ],
    'admin' => [
        'classicgames' => 'https://playclassic.games/',
        'eaglrcraft'   => 'https://eaglercraft.com/mc/1.8.8',
        'codzombies'   => 'https://nzp.gay/',
        'addicting'    => 'https://www.addictinggames.com/'
    ],
    'owner' => [
        'classicgames' => 'https://playclassic.games/',
        'eaglrcraft'   => 'https://eaglercraft.com/mc/1.8.8',
        'codzombies'   => 'https://nzp.gay/',
        'addicting'    => 'https://www.addictinggames.com/',
        'google'       => 'https://www.google.com/',
        'webmin'       => 'https://192.168.1.131:10000/sysinfo.cgi?xnavigation=1',
        'chatgpt'      => 'https://chat.openai.com/'
    ]
];

if (!isset($_GET['tab'])) {
    http_response_code(400);
    exit('Missing tab');
}
$tab = (string)$_GET['tab'];
$allowed = $TAB_MAP[$role] ?? $TAB_MAP['user'];
if (!isset($allowed[$tab])) {
    http_response_code(403);
    exit('Tab not allowed');
}

$targetBase = rtrim($allowed[$tab], '/') . '/';
$reqPath = ltrim((string)($_GET['p'] ?? ''), '/');
$targetUrl = $targetBase . $reqPath;

$ch = curl_init($targetUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => true,
    CURLOPT_SSL_VERIFYPEER => true, // Set to false ONLY if you must reach self-signed HTTPS.
    CURLOPT_TIMEOUT => 25,
    CURLOPT_ENCODING => '', // Accept gzip/deflate
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0',
    CURLOPT_HTTPHEADER => [
        'Accept: ' . ($_SERVER['HTTP_ACCEPT'] ?? '*/*'),
        'Accept-Language: ' . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en-US,en;q=0.9'),
        'Referer: ' . $targetBase
    ]
]);
$resp = curl_exec($ch);
if ($resp === false) {
    http_response_code(502);
    exit('Upstream fetch failed');
}
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($resp, $headerSize);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'text/html; charset=utf-8';
curl_close($ch);

// If HTML, inject <base href="..."> so relative assets load from the upstream site
if (stripos($contentType, 'text/html') !== false) {
    $hasHead = stripos($body, '<head') !== false;
    $baseTag = '<base href="' . htmlspecialchars($targetBase, ENT_QUOTES, 'UTF-8') . '">';
    if ($hasHead) {
        $body = preg_replace('~(<head[^>]*>)~i', '$1' . $baseTag, $body, 1);
    } else {
        $body = "<!doctype html><html><head>{$baseTag}</head><body>{$body}</body></html>";
    }
}

header('Content-Type: ' . $contentType);
echo $body;
