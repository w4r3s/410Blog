<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../connect.php';

$postId = isset($_GET['post_id']) ? $_GET['post_id'] : null;
$post = null;

if ($postId) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contentMarkdown = $_POST['content'];
    $sanitizedContent = sanitizeMarkdownContent($contentMarkdown);

    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ?");
    $stmt->execute([$title, $sanitizedContent, $postId]);

    header("Location: ../blog.php");
    exit();
}

$contentToEdit = $post ? htmlspecialchars_decode($post['content'], ENT_QUOTES) : '';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
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
            width: 100%;
            max-width: 800px;
        }

        label, input, textarea, button {
            font-family: 'Iosevka Aile', sans-serif;
            margin-bottom: 1em;
            display: block;
            width: 100%;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Iosevka Etoile', serif;
        }

        input[type="text"], textarea {
            padding: 0.5em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #9E0144;
            color: white;
            border: none;
            cursor: pointer;
            padding: 0.5em 1em;
            border-radius: 5px;
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
    <?php if ($post): ?>
        <form action="post_edit.php?post_id=<?php echo $postId; ?>" method="post">
            <h1>Edit Post</h1>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            
            <label for="content">Content (Markdown format):</label>
            <!-- Display original Markdown content for editing -->
            <textarea id="content" name="content" rows="10" cols="30" required><?php echo htmlspecialchars_decode($post['content'], ENT_QUOTES); ?></textarea>
            
            <button type="submit" name="update">Update</button>
        </form>
    <?php else: ?>
        <p>Post does not exist or cannot be edited.</p>
    <?php endif; ?>
</body>
</html>