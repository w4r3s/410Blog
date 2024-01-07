<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../connect.php';

// 自定义函数，用于移除非Markdown代码块中的HTML标签
function sanitizeMarkdownContent($text) {
    // 匹配Markdown代码块
    $markdownCodeBlockPattern = '/(```[a-z]*\n[\s\S]*?\n```|`[^`]*`)/';

    // 分割文本为Markdown代码块和其他部分
    $parts = preg_split($markdownCodeBlockPattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $sanitizedText = '';

    foreach ($parts as $index => $part) {
        if ($index % 2 == 0) {
            // 非代码块部分，移除HTML标签
            $sanitizedText .= strip_tags($part);
        } else {
            // 代码块部分保持不变
            $sanitizedText .= $part;
        }
    }

    return $sanitizedText;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $contentMarkdown = $_POST['content'];
    // 应用自定义函数处理Markdown内容
    $sanitizedContent = sanitizeMarkdownContent($contentMarkdown);
    $tags = array_map('trim', explode(',', $_POST['tags']));
    $user_id = $_SESSION['user_id'];

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $sanitizedContent]);
        $postId = $pdo->lastInsertId();

        foreach ($tags as $tagName) {
            $tagNameSafe = htmlspecialchars($tagName, ENT_QUOTES, 'UTF-8');
            $tagStmt = $pdo->prepare("SELECT tag_id FROM tags WHERE name = ?");
            $tagStmt->execute([$tagNameSafe]);
            $tag = $tagStmt->fetch(PDO::FETCH_ASSOC);

            if (!$tag) {
                $tagStmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                $tagStmt->execute([$tagNameSafe]);
                $tagId = $pdo->lastInsertId();
            } else {
                $tagId = $tag['tag_id'];
            }

            $postTagStmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $postTagStmt->execute([$postId, $tagId]);
        }

        $pdo->commit();
        header("Location: ../blog.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a post</title>
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
        main {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            text-align: left;
            background: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        label, input, textarea, button {
            font-family: 'Iosevka Aile', sans-serif;
            margin-bottom: 0.5em;
            display: block;
            width: 100%;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Iosevka Etoile', serif;
        }
        input, textarea, button {
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
    </style>
</head>
<body>
    <main>
        <form action="edit.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="content">Content (Markdown format):</label>
            <textarea id="content" name="content" rows="10" cols="30" required></textarea>

            <label for="tags">Tags (separated by commas):</label>
            <input type="text" id="tags" name="tags">
            
            <button type="submit">Post</button>
        </form>
    </main>
</body>
</html>