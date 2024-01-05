<?php
session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'version.php';
$currentVersion = getCurrentVersion(); 
$updateMessage = '';

if (isset($_POST['check_updates'])) {
    checkForUpdates($updateMessage, $currentVersion);
}

$currentDate = date('Y-m-d'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator System</title>
    <style>
        @font-face {
            font-family: 'Iosevka Aile';
            src: url('../fonts/IosevkaEtoile-Regular.ttc') format('truetype');
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
            color: #333;
        }
        .container {
            text-align: center;
            background: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-family: 'Iosevka Etoile', serif;
            color: #444;
            margin-bottom: 0.5em;
        }
        p {
            color: #666;
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
        form {
            margin-top: 1em;
        }
        input[type=submit] {
            background-color: #9E0144;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        input[type=submit]:hover {
            background-color: #d63384;
        }
        .update-message {
            color: #008000;
            font-weight: bold;
            margin-top: 1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>Today is: <?php echo $currentDate; ?></p>
        <p>The Version: <?php echo $currentVersion; ?></p>

        <form action="admin.php" method="post">
            <input type="submit" name="check_updates" value="Check for updates">
        </form>

        <div class="update-message">
            <?php echo $updateMessage; ?>
        </div>

        <a href="edit.php">Write a post</a><br>
        <a href="pwd_change.php">Change password</a><br>
        <a href="all_post.php">Manage posts</a><br>
        <a href="index_edit.php">Edit home page</a>
    </div>
</body>
</html>