<?php
session_start();
require_once '../config.php';
require_once '../connect.php';

// 验证用户是否登录
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

//LANG
$lang = require __DIR__ . '/../lang/lang_' . CURRENT_LANG . '.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 使用更安全的密码散列选项
        $options = ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2];
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID, $options);

        // 确保只有当前登录用户可以更改自己的密码
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username AND user_id = :user_id");
        $stmt->execute([':password' => $hashedPassword, ':username' => $username, ':user_id' => $_SESSION['user_id']]);

        $message = "Password reset successful for user: " . htmlspecialchars($username);
    } catch (PDOException $e) {
        $message = "Password reset failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['reset_password']; ?></title>
    <style>
        @font-face {
            font-family: 'Iosevka Aile';
            src: url('../fonts/IosevkaAile-Regular.ttf') format('truetype');
        }

        @font-face {
            font-family: 'Iosevka Etoile';
            src: url('../fonts/IosevkaEtoile-Regular.ttf') format('truetype');
        }

        body {
            background-color: #F8F5D7;
            font-family: 'Iosevka Aile', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        label, input, button {
            font-family: 'Iosevka Aile', sans-serif;
            margin-bottom: 1em;
            display: block;
            width: 100%;
        }

        h1 {
            font-family: 'Iosevka Etoile', serif;
            color: #444;
        }

        input, button {
            padding: 0.5em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #9E0144;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #d63384;
        }

        a {
            color: #9E0144;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #d63384;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="pwd_change.php" method="post">
        <label for="username"><?php echo $lang['username']; ?></label><br>
        <input type="text" id="username" name="username" required><br>

        <label for="new_password"><?php echo $lang['new_password']; ?></label><br>
        <input type="password" id="new_password" name="new_password" required><br>

        <button type="submit"><?php echo $lang['save_changes']; ?></button>
    </form>
</body>
</html>
