<?php
session_start();
require_once '../config.php';
require_once '../connect.php';

// Handle login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = MD5($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: admin.php");
        exit();
    } else {
        echo "<p>Wrong user name or password</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login</title>
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
    <form action="login.php" method="post">
        <h1>Administrator Login</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">LOGIN</button>
    </form>
</body>
</html>