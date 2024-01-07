<?php
session_start();
require_once '../config.php';
require_once '../connect.php';
require_once '../vendor/autoload.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// 自定义函数，用于移除非Markdown代码块中的HTML标签
function sanitizeMarkdownContent($text) {
    $markdownCodeBlockPattern = '/(```[a-z]*\n[\s\S]*?\n```|`[^`]*`)/';
    $parts = preg_split($markdownCodeBlockPattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $sanitizedText = '';

    foreach ($parts as $index => $part) {
        if ($index % 2 == 0) {
            $sanitizedText .= strip_tags($part);
        } else {
            $sanitizedText .= $part;
        }
    }

    return $sanitizedText;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contentMarkdown = $_POST['content'];
    // 应用自定义过滤函数
    $contentMarkdownSanitized = sanitizeMarkdownContent($contentMarkdown);

    // Update home page content with sanitized Markdown
    $stmt = $pdo->prepare("REPLACE INTO homepage (id, content) VALUES (1, ?)");
    $stmt->execute([$contentMarkdownSanitized]);
}

// Get the current content of the home page as Markdown
$stmt = $pdo->prepare("SELECT content FROM homepage WHERE id = 1");
$stmt->execute();
$homepage = $stmt->fetch(PDO::FETCH_ASSOC);
$currentContent = $homepage ? htmlspecialchars_decode($homepage['content'], ENT_QUOTES) : '';

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homepage</title>
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
            font-family: 'Iosevka Aile', sans-serif;
            background-color: #F8F5D7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        h1 {
            font-family: 'Iosevka Etoile', sans-serif;
        }

        form {
            width: 80%;
            max-width: 960px;
            margin: 20px auto;
            padding: 20px;
            box-sizing: border-box;
        }
        
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #9E0144;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 10px;
            cursor: pointer;
        }
        
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <center><h1>Edit Homepage Content</h1></center>
    <form action="index_edit.php" method="post">
        <label for="content">Homepage Content (Markdown supported):</label><br>
        <textarea id="content" name="content" rows="10" cols="30"><?php echo $currentContent; ?></textarea><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
