<?php
session_start();

require_once '../config.php';
require_once '../connect.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Update password in database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
            $message = 'Password updated successfully!';
        } else {
            $message = 'Password update failed!';
        }
    } else {
        $message = 'The passwords entered twice do not match!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        @font-face {
            font-family: 'Iosevka Aile';
            src: url('../fonts/IosevkaAile-Regular.ttc') format('truetype');
        }

        @font-face {
            font-family: 'Iosevka Etoile';
            src: url('../fonts/IosevkaEtoile-Regular.ttc') format('truetype');
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
        main {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            text-align: left;
            background: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        label, input, button {
            font-family: 'Iosevka Aile', sans-serif;
            margin-bottom: 0.5em;
            display: block;
            width: 100%;
        }
        h1 {
            font-family: 'Iosevka Etoile', serif;
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
    <main>
        <h1>Change Password</h1>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="pwd_change.php" method="post">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Change Password</button>
        </form>

        <a href="admin.php">Return to Admin Panel</a>
    </main>
</body>
</html>