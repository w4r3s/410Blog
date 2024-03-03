<?php
session_start();

require_once '../config.php';
require_once '../connect.php';

$message = '';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

function parseYamlFrontMatter($text) {
    $frontMatter = array('title' => '', 'tags' => []);
    $pattern = '/^---(.*?)---/s';
    if (preg_match($pattern, $text, $matches)) {
        $yamlPart = $matches[1];
        if (preg_match('/title:\s*"(.+?)"/', $yamlPart, $titleMatches)) {
            $frontMatter['title'] = $titleMatches[1];
        }
        if (preg_match('/tags:\s*\[(.*?)\]/', $yamlPart, $tagsMatches)) {
            $tagsString = $tagsMatches[1];
            $frontMatter['tags'] = array_map('trim', explode(',', str_replace('"', '', $tagsString)));
        }
    }
    return $frontMatter;
}

function sanitizeMarkdownContent($text) {
    return preg_replace('/^---.*?---/s', '', $text, 1);
}

function extractTitleFromFirstLine($text) {
    $lines = explode("\n", $text);
    $titleLine = trim(array_shift($lines));
    return trim($titleLine, "# ");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['markdownFiles']['name'])) {
    $totalFiles = count($_FILES['markdownFiles']['name']);
    $titleSource = $_POST['titleSource'] ?? 'yaml'; // 确保有默认值

    for ($i = 0; $i < $totalFiles; $i++) {
        $fileName = $_FILES['markdownFiles']['name'][$i];
        $fileError = $_FILES['markdownFiles']['error'][$i];
        $fileTmpName = $_FILES['markdownFiles']['tmp_name'][$i];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileError === UPLOAD_ERR_OK) {
            // 检查文件扩展名是否为md
            if ($fileExtension === "md") {
                $contentMarkdown = file_get_contents($fileTmpName);
                $frontMatter = parseYamlFrontMatter($contentMarkdown);
                $sanitizedContent = sanitizeMarkdownContent($contentMarkdown);
                $title = ($titleSource === 'first-line') ? extractTitleFromFirstLine($sanitizedContent) : $frontMatter['title'];
                $tags = $frontMatter['tags'];
                $user_id = $_SESSION['user_id'];

                try {
                    $pdo->beginTransaction();
                    
                    // 插入帖子
                    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $title, $sanitizedContent]);
                    $postId = $pdo->lastInsertId();
                
                    // 处理每个标签
                    foreach ($tags as $tagName) {
                        $tagNameSafe = htmlspecialchars(trim($tagName), ENT_QUOTES, 'UTF-8');
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
                    $message .= "File " . htmlspecialchars($fileName) . " uploaded successfully!<br>";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $message .= "An error occurred while processing file " . htmlspecialchars($fileName) . ": " . $e->getMessage() . "<br>";
                }
            } else {
                $message .= "File " . htmlspecialchars($fileName) . " is not a Markdown file and was skipped.<br>";
            }
        } else {
            $message .= "Upload failed for file " . htmlspecialchars($fileName) . " with error code " . $fileError . "<br>";
        }
    }

}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Post from Markdown</title>
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
        input[type="file"] {
            border: none;
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
        <h1>Recover Post from Markdown</h1>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="recover.php" method="post" enctype="multipart/form-data">
            <label for="markdownFiles">Markdown Files:</label>
            <input type="file" id="markdownFiles" name="markdownFiles[]" multiple required>
            
            <label>Title Source:</label>
            <label><input type="radio" name="titleSource" value="yaml" checked> Extract from YAML Front Matter</label>
<!--
            <label><input type="radio" name="titleSource" value="first-line"> Use First Line as Title</label>
        -->
            <button type="submit">Upload Files</button>
        </form>
    </main>
</body>
</html>
