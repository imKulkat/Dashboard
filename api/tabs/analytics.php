<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Please log in.');
}

$current = find_user($_SESSION['username']) ?? null;
if (!$current || ($current['role'] ?? 'user') !== 'owner') {
    http_response_code(403);
    exit('Forbidden');
}

$users = load_users();
?>
<div class="panel">
  <h2>Analytics</h2>
  <p>Manage user roles and view all registered accounts.</p>
  <div style="overflow-x:auto;">
    <table style="width:100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:0.6rem; border-bottom:1px solid var(--border);">Username</th>
          <th style="text-align:left; padding:0.6rem; border-bottom:1px solid var(--border);">Role</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u):
          $uname = $u['username'] ?? '';
          $urole = $u['role'] ?? 'user';
        ?>
        <tr>
          <td style="padding:0.6rem; border-bottom:1px solid var(--border);"><?= htmlspecialchars($uname) ?></td>
          <td style="padding:0.6rem; border-bottom:1px solid var(--border);"><?= htmlspecialchars($urole) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
