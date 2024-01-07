<?php
require_once 'config.php';
require_once 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Articles - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="blog-articles">
        <h2>Blog Articles</h2>
        <p>If you have a compatible reader, be sure to check out my <a href="generate_rss.php">RSS feed</a> for automatic updates. Also check out the <a href="generate_jsonfeed.php">JSONFeed</a>.</p>
        
        <?php
        $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = date('d/m/Y', strtotime($row['created_at']));
            echo "<article>";
            echo "<h3><a href='article-link.php?post_id={$row['post_id']}'>{$date} - " . htmlspecialchars($row['title']) . "</a></h3>";
            echo "</article>";
        }
        ?>

        <?php include 'footer.php'; ?>
    </main>
</body>
</html>
