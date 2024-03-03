<?php
session_start();

require_once '../config.php';
require_once '../connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$message = '';

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['markdownFiles']['name'])) {
    $totalFiles = count($_FILES['markdownFiles']['name']);

    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['markdownFiles']['error'][$i] === UPLOAD_ERR_OK) {
            $contentMarkdown = file_get_contents($_FILES['markdownFiles']['tmp_name'][$i]);
            $sanitizedContent = sanitizeMarkdownContent($contentMarkdown);

            // Assuming title and tags extraction logic here
            // This needs to be adapted based on your file naming conventions and requirements
            $title = 'Post Title'; // Placeholder title
            $tags = ['default']; // Placeholder tags

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $title, $sanitizedContent]);
                $postId = $pdo->lastInsertId();

                foreach ($tags as $tagName) {
                    // Insert tags logic here
                }

                $pdo->commit();
                $message .= "File " . $_FILES['markdownFiles']['name'][$i] . " uploaded successfully!<br>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message .= "An error occurred: " . $e->getMessage() . "<br>";
            }
        } else {
            $message .= "Upload failed for file " . $_FILES['markdownFiles']['name'][$i] . " with error code " . $_FILES['markdownFiles']['error'][$i] . "<br>";
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
            <button type="submit">Upload Files</button>
        </form>
    </main>
</body>
</html>
