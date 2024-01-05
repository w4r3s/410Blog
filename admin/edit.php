<?php
session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}


require_once '../vendor/autoload.php';

require_once '../config.php';
require_once '../connect.php';


$parsedown = new Parsedown();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $parsedown->text($_POST['content']);
    $tags = explode(',', $_POST['tags']); 
    $user_id = $_SESSION['user_id'];

    
    $pdo->beginTransaction();

    try {
      
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $content]);

        $postId = $pdo->lastInsertId();

        
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (!empty($tagName)) {
                
                $tagStmt = $pdo->prepare("SELECT tag_id FROM tags WHERE name = ?");
                $tagStmt->execute([$tagName]);
                $tag = $tagStmt->fetch(PDO::FETCH_ASSOC);

                if (!$tag) {
                    
                    $tagStmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                    $tagStmt->execute([$tagName]);
                    $tagId = $pdo->lastInsertId();
                } else {
                    $tagId = $tag['tag_id'];
                }

                
                $postTagStmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                $postTagStmt->execute([$postId, $tagId]);
            }
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a post</title>
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