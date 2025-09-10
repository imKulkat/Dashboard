<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Not logged in');
}

$user = find_user($_SESSION['username']);
if (!$user) {
    http_response_code(403);
    exit('User not found');
}

$role = $user['role'] ?? 'user';
$TABS = require __DIR__ . '/tabs_config.php';

// Convert label=>URL to key=>URL for proxy lookups
$allowedTabs = [];
foreach ($TABS[$role] ?? $TABS['user'] as $label => $url) {
    if (preg_match('/tab=([^&]+)/', $url, $m)) {
        $allowedTabs[$m[1]] = preg_replace('/\?.*/', '', $url);
    }
}

if (!isset($_GET['tab'])) {
    http_response_code(400);
    exit('Missing tab');
}

$tab = (string)$_GET['tab'];

if (!isset($allowedTabs[$tab])) {
    http_response_code(403);
    exit('Tab not allowed');
}

$targetBase = rtrim($allowedTabs[$tab], '/') . '/';
$reqPath = ltrim((string)($_GET['p'] ?? ''), '/');
$targetUrl = $targetBase . $reqPath;

$ch = curl_init($targetUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_ENCODING => '',
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

if (stripos($contentType, 'text/html') !== false) {
    $baseTag = '<base href="' . htmlspecialchars($targetBase, ENT_QUOTES, 'UTF-8') . '">';
    if (stripos($body, '<head') !== false) {
        $body = preg_replace('~(<head[^>]*>)~i', '$1' . $baseTag, $body, 1);
    } else {
        $body = "<!doctype html><html><head>{$baseTag}</head><body>{$body}</body></html>";
    }
}

header('Content-Type: ' . $contentType);
echo $body;
