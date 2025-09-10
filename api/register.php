<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php'; // Uses the Supabase config we made earlier

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = 'user'; // Default role for new signups

    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    } elseif (find_user($username)) {
        $error = "Username already exists.";
    } else {
        if (add_user($username, $password, $role)) {
            $_SESSION['username'] = $username;
            header('Location: /');
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h1>Register</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Register</button>
</form>
<p><a href="/login">Already have an account? Login here</a></p>
</body>
</html>
