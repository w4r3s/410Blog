<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$post_id = $_GET['post_id'] ?? null;

$post = null;
$tags = [];

if ($post_id) {
    // Get blog post from database
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Get the tags of the blog post
        $tagStmt = $pdo->prepare("SELECT name FROM tags JOIN post_tags ON tags.tag_id = post_tags.tag_id WHERE post_tags.post_id = ?");
        $tagStmt->execute([$post_id]);
        while ($tag = $tagStmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = "<a href='tags.php?tag=" . urlencode($tag['name']) . "'>" . htmlspecialchars($tag['name']) . "</a>";
        }

        // Define additional variables
        $publishedDate = date('d/m/Y', strtotime($post['created_at']));
        $wordCount = str_word_count(strip_tags($post['content']));
        $readingTime = ceil($wordCount / 200); // Assuming average reading speed of 200 words per minute
    }
}

$parsedown = new Parsedown();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post['title']) : 'Post not found'; ?> - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles/styles/atom-one-dark.css">
    <script src="styles/highlight.min.js"></script>
    <style>
    .post-content img {
        max-width: 960px;
        max-height: 400px;
        width: auto; /* 保持图片的原始宽高比 */
        height: auto; /* 保持图片的原始宽高比 */
    }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <main class="blog-articles">
        <?php if ($post): ?>
            <article>
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <p style="font-size: 14px; color: gray;">
                    Published on <?php echo $publishedDate; ?>, 
                    <?php echo $wordCount; ?> words, 
                    <?php echo $readingTime; ?> minute<?php echo ($readingTime == 1) ? '' : 's'; ?> to read
                </p>
                <div class="post-content"><?php echo $parsedown->text($post['content']); ?></div>
                <?php if (!empty($tags)): ?>
                    <p class="post-tags">Tags: <?php echo implode(', ', $tags); ?></p>
                <?php endif; ?>
            </article>
            

        <?php else: ?>
            <p>Post not found or invalid post ID.</p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script>hljs.highlightAll();</script>
</body>
</html>