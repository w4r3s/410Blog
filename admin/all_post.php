<?php
session_start();

require_once '../config.php';
require_once '../connect.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $selectedPosts = $_POST['selected_posts'] ?? [];
    if (!empty($selectedPosts)) {
        // Delete tags associated with blog posts
        $placeholders = implode(',', array_fill(0, count($selectedPosts), '?'));
        $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id IN ($placeholders)");
        $stmt->execute($selectedPosts);

        // Delete blog post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id IN ($placeholders)");
        $stmt->execute($selectedPosts);
    }
}

// Get all blog posts
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
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

        main {
            width: 80%;
            max-width: 800px;
            margin: 2em auto;
            background: white;
            padding: 1em;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-family: 'Iosevka Etoile', serif;
        }

        .select-all, div {
            margin-bottom: 0.5em;
        }

        input, button {
            margin-right: 0.5em;
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

        button {
            background-color: #9E0144;
            color: white;
            border: none;
            padding: 0.5em 1em;
            border-radius: 5px;
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
        <h1>Manage Posts</h1>
        <p>Total of <?php echo count($posts); ?> posts.</p>

        <form action="all_post.php" method="post">
            <div class="select-all">
                <input type="checkbox" id="select_all" onclick="toggleAll(this)">
                <label for="select_all">Select All</label>
            </div>

            <?php foreach ($posts as $post): ?>
                <div>
                    <input type="checkbox" class="selected_posts" name="selected_posts[]" value="<?php echo $post['post_id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?> - <?php echo $post['created_at']; ?>
                    <a href="post_edit.php?post_id=<?php echo $post['post_id']; ?>">Edit</a>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="delete">Delete Selected Posts</button>
        </form>

        <a href="admin.php">Return to Admin Panel</a>
    </main>

    <script>
        function toggleAll(source) {
            checkboxes = document.querySelectorAll('.selected_posts');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</body>
</html>