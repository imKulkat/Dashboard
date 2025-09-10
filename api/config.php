<?php
declare(strict_types=1);

define('USERS_FILE', __DIR__ . '/users.json');
define('TABS_DIR', __DIR__ . '/tabs');

function load_users(): array {
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([], JSON_PRETTY_PRINT));
        @chmod(USERS_FILE, 0664);
    }
    $raw = file_get_contents(USERS_FILE);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = [];
    return array_values($data);
}

function save_users(array $users): void {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
    @chmod(USERS_FILE, 0664);
}

function find_user(string $username): ?array {
    foreach (load_users() as $u) {
        if (strcasecmp($u['username'] ?? '', $username) === 0) return $u;
    }
    return null;
}

function upsert_user(array $user): void {
    $users = load_users();
    $found = false;
    foreach ($users as &$u) {
        if (strcasecmp($u['username'] ?? '', $user['username']) === 0) {
            $u = array_merge($u, $user);
            $found = true;
            break;
        }
    }
    if (!$found) $users[] = $user;
    save_users($users);
}

// Bootstrap owner
$hasOwner = false;
foreach (load_users() as $u) {
    if (($u['role'] ?? 'user') === 'owner') { $hasOwner = true; break; }
}
if (!$hasOwner) {
    upsert_user([
        'username' => 'owner',
        'password' => password_hash('owner123', PASSWORD_DEFAULT),
        'role' => 'owner',
        'created_at' => date('c')
    ]);
}
