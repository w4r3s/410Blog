<?php
$configFile = 'config.php';

if (file_exists($configFile) && filesize($configFile) > 0) {
    die("CMS has been installed.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminUsername = $_POST['admin_username'];
    $adminPassword = $_POST['admin_password'];
    $blogTitle = $_POST['blog_title'];
    $dbHost = $_POST['db_host'];
    $dbUser = $_POST['db_user'];
    $dbPass = $_POST['db_pass'];
    $dbName = $_POST['db_name']; 
    $blogUrl = $_POST['blog_url'];

    // Check if the URL has the correct format
    if (!preg_match("/^(http:\/\/|https:\/\/).+$/", $blogUrl)) {
        die("Unsupported format. The URL must start with http:// or https://.");
    }

    $configContent = "<?php\n";
    $configContent .= "define('BLOG_TITLE', '$blogTitle');\n";
    $configContent .= "define('DB_HOST', '$dbHost');\n";
    $configContent .= "define('DB_USER', '$dbUser');\n";
    $configContent .= "define('DB_PASS', '$dbPass');\n";
    $configContent .= "define('DB_NAME', '$dbName');\n";
    $configContent .= "define('BLOG_URL', '$blogUrl');\n";
    $configContent .= "define('BLOG_BACKGROUND_COLOR', '#F8F5D7');\n";
    $configContent .= "define('BLOG_FONT_PROSE', 'Iosevka Aile');\n";
    $configContent .= "define('BLOG_FONT_CODE', 'Iosevka Curly');\n";
    $configContent .= "define('BLOG_FONT_TITLE', 'Iosevka Etoile');\n";
    $configContent .= "define('DATE_FORMAT', 'd/m/Y');\n";
    $configContent .= "?>";

    file_put_contents($configFile, $configContent);

    try {
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName");
        $pdo->exec("use $dbName");
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL
        )");

        //$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        $options = ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2];
        $hashedPassword = password_hash($adminPassword, PASSWORD_ARGON2ID, $options);

        $pdo->exec("INSERT INTO users (username, password) VALUES ('$adminUsername', '$hashedPassword')");
        
        echo "Successful installation！";
    } catch (PDOException $e) {
        die("Installation failed: " . $e->getMessage());
    }
} else {

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>410Blog Installer</title>
    <style>
        body {
            background-color: #F8F5D7;
            font-family: "Iosevka Aile", sans-serif;
            padding: 20px;
        }
        h1 {
            font-family: "Iosevka Etoile", sans-serif;
        }
        main {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            text-align: left;
        }
        input[type="text"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <main>
        <h1>410Blog Installer</h1>
        <form action="installer.php" method="post">
        <label for="blog_title">Title:</label><br>
        <input type="text" id="blog_title" name="blog_title" required><br>

        <label for="blog_url">Blog URL (http:// or https://):</label><br>
        <input type="text" id="blog_url" name="blog_url" value="http://" required><br>

        <label for="db_host">Database Host:</label><br>
        <input type="text" id="db_host" name="db_host" required><br>

        <label for="db_user">Database Username:</label><br>
        <input type="text" id="db_user" name="db_user" required><br>

        <label for="db_pass">Database Password:</label><br>
        <input type="password" id="db_pass" name="db_pass" required><br>

        <label for="db_name">Database Name:</label><br>
        <input type="text" id="db_name" name="db_name" value="410blog" required><br>

        <label for="admin_username">Admin Username:</label><br>
        <input type="text" id="admin_username" name="admin_username" required><br>

        <label for="admin_password">Admin Password:</label><br>
        <input type="password" id="admin_password" name="admin_password" required><br>

        <button type="submit">Next</button>
        </form>
    </main>
</body>
</html>';
}
?>
