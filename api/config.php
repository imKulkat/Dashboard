<?php
declare(strict_types=1);
session_start();

require __DIR__ . '/../vendor/autoload.php'; // Composer autoload

use PHPSupabase\Service;

// === CONFIGURE THESE ===
$SUPABASE_URL = 'https://YOUR_PROJECT_ID.supabase.co';
$SUPABASE_KEY = 'YOUR_ANON_PUBLIC_KEY';
// =======================

// Create Supabase service
$supabase = new Service($SUPABASE_KEY, $SUPABASE_URL);
$db = $supabase->createDatabase();

/**
 * Fetch a user by username
 */
function find_user(string $username): ?array {
    global $db;
    $result = $db->from('users')
                 ->select('*')
                 ->eq('username', $username)
                 ->execute();

    if (!empty($result) && isset($result[0])) {
        return $result[0];
    }
    return null;
}

/**
 * Add a new user
 */
function add_user(string $username, string $password, string $role = 'user'): bool {
    global $db;
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $result = $db->from('users')
                 ->insert([
                     'username' => $username,
                     'password_hash' => $hash,
                     'role' => $role
                 ])
                 ->execute();

    return !empty($result);
}

/**
 * Verify login credentials
 */
function verify_login(string $username, string $password): bool {
    $user = find_user($username);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $username;
        return true;
    }
    return false;
}
