<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $options = ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2];
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID, $options);

        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
        $stmt->execute([':password' => $hashedPassword, ':username' => $username]);

        echo "Password reset successful for user: " . htmlspecialchars($username);
    } catch (PDOException $e) {
        die("Password reset failed: " . $e->getMessage());
    }
} else {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form action="rest.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>';
}
?>
