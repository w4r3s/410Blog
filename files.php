<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'vendor/autoload.php';

$parsedown = new Parsedown();

// 这里可以添加获取文件列表的逻辑，如果您决定分享文件
$filesAvailable = false; // 假设目前没有文件可供分享

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="styles/styles/atom-one-dark.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <main class="container">
        <h1>Shared Files</h1>
        <?php if ($filesAvailable): ?>
            <!-- 如果有文件可分享，这里可以展示文件列表 -->
        <?php else: ?>
            <p>There are currently no files available for sharing.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
   
    <script src="styles/highlight.min.js"></script>
    <script>console.log('highlight.js should be loaded');</script>
    <script>hljs.highlightAll();</script>
</body>
</html>
