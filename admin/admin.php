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
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .block {
            background: #fff;
            border-radius: 8px;
            padding: 10px 20px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-block {
            text-align: center;
        }

        .content-block {
            display: flex;
            justify-content: space-between;
        }

        .update-block {
            flex-grow: 2;
            margin-right: 20px;
        }

        h1 {
            font-family: 'Iosevka Etoile', serif;
            color: #444;
            margin: 0.5em 0;
        }

        p, .update-message {
            color: #666;
        }

        a, input[type=submit] {
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

        a:hover, input[type=submit]:hover {
            background-color: #d63384;
        }

        .links-block {
            text-align: center;
        }

        .update-message {
            font-weight: bold;
            margin-top: 1em;
        }
        .content-block {
            display: flex;
            justify-content: space-between;
        }

        .update-block {
            /* Adjust width as needed */
            width: 48%;
        }

        .right-side {
            /* Adjust width as needed */
            width: 48%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .update-message {
            /* Styles for the update message */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Welcome and Date Block -->
        <div class="block header-block">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <p>Today is: <?php echo date('Y-m-d'); ?></p>
        </div>

        <!-- Version, Update, and Right Side Info Block -->
        <div class="content-block">
            <div class="block update-block">
                <p>Current Version: <?php echo htmlspecialchars($currentVersion); ?></p>
                <?php if ($updateMessage): ?>
                    <div class="update-message"><?php echo $updateMessage; ?></div>
                <?php endif; ?>
                <form action="admin.php" method="post">
                    <input type="submit" name="check_updates" value="Check for Updates">
                </form>
            </div>

            <div class="block right-side">
                <p>Current IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?></p>
                <p>Last Login: <?php echo htmlspecialchars($_SESSION['last_login_time'] ?? 'First login'); ?></p>
                <!-- Add more right-side information here if needed -->
            </div>
        </div>

        <!-- Links Block -->
        <div class="block links-block">
            <a href="edit.php">Write a Post</a>
            <a href="pwd_change.php">Change Password</a>
            <a href="all_post.php">Manage Posts</a>
            <a href="index_edit.php">Edit Home Page</a>
        </div>
    </div>
</body>
</html>
