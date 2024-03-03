<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'version.php';
$currentVersion = getCurrentVersion(); 
$updateMessage = '';
$newsItems = ''; // Initialize $newsItems to ensure it's always set







if (isset($_POST['check_updates'])) {
    // Assuming checkForUpdates now populates $newsItems as well
    checkForUpdates($updateMessage, $newsItems, $currentVersion);
}

$currentDate = date('Y-m-d'); 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator System</title>
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

        .update-news {
            list-style-type: none; /* No bullets */
            padding: 0;
            margin: 0;
        }

        .update-news li {
            background-color: #ffe599; /* Light yellow background */
            border-left: 5px solid #9E0144; /* Thick left border */
            padding: 5px 10px;
            margin: 5px 0;
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .news-update-container {
            background-color: #f8f5d7; /* Same as body bg color */
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .news-update-container h2 {
            color: #9E0144; /* Similar to button bg color */
            font-family: 'Iosevka Etoile', serif;
            font-size: 1.5em;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 1em;
            float: right;

        }

        .links-block, .button-container {
        text-align: center;
        display: flex;
        justify-content: center; /* Centers the content horizontally */
        align-items: center; /* Centers the content vertically */
        flex-wrap: wrap; /* Allows items to wrap onto the next line */
        gap: 10px; /* Adds space between the items */
        margin-top: 20px; /* Adds space above the block */
    }

    .links-block a, .button-container form {
        margin: 4px; /* Adds space around each link and form */
    }

    .button-container {
        order: 1; /* Ensures this container comes first */
    }

    .links-block {
        order: 2; /* Ensures the links come after the buttons */
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
            </div>

            <div class="block right-side">
                <p>Current IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?> & Last Login: <?php echo htmlspecialchars($_SESSION['last_login_time'] ?? 'First login'); ?></p>
                <!--<p>Last Login: <?php echo htmlspecialchars($_SESSION['last_login_time'] ?? 'First login'); ?></p>-->
                
                <div class="button-container">
                    <div class="backup-instruction">
                        <h4>⚠️ Note: Database Backup</h4>
                        <p>When you get a 0-byte file, it typically indicates that the mysqldump command did not execute successfully. There can be various reasons for this:</p>
                        <ul>
                            <li>The mysqldump command might not be available in the PATH environment variable accessible by the PHP execution environment.</li>
                            <li>The database credentials in the config.php might be incorrect.</li>
                            <li>The user associated with the database might not have the necessary permissions to perform the dump.</li>
                            <li>There could be issues with the passthru function, such as disabled exec functions in your PHP configuration (disable_functions in php.ini).</li>
                        </ul>
                    </div>



                </div>
            </div>
        </div>


        <div class="content-block">
    <div class="button-container">
        <form action="db_backup.php" method="post">
            <input type="submit" value="Database Backup">
        </form>
        <form action="admin.php" method="post">
            <input type="submit" name="check_updates" value="Check for Updates">
        </form>
    </div>
    <div class="links-block">
        <a href="edit.php" target="_blank">Write a Post</a>
        <a href="pwd_change.php" target="_blank">Change Password</a>
        <a href="all_post.php" target="_blank">Manage Posts</a>
        <a href="index_edit.php" target="_blank">Edit Home Page</a>
        <a href="generate_static.php" target="_blank">Generate Static Pages</a>
        <a href="recover.php" target="_blank">Recover Posts</a>
        <a href="imghost.php" target="_blank">Image Hosting</a>
    </div>
</div>



</body>
</html>