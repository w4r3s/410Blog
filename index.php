<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'vendor/autoload.php';

$parsedown = new Parsedown();

// Get home page content
$stmt = $pdo->prepare("SELECT content FROM homepage WHERE id = 1");
$stmt->execute();
$homepage = $stmt->fetch(PDO::FETCH_ASSOC);
$homepageContent = $homepage ? $parsedown->text($homepage['content']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="styles/styles/atom-one-dark.css">
    <style>
    main img {
        max-width: 960px;
        max-height: 400px;
        width: auto; /* 保持图片的原始宽高比 */
        height: auto; /* 保持图片的原始宽高比 */
    }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <main>
        <?php echo $homepageContent; ?>
    </main>
    <?php include 'footer.php'; ?>
   
    <script src="styles/highlight.min.js"></script>
    <script>console.log('highlight.js should be loaded');</script>
    <script>hljs.highlightAll();</script>
</body>
</html>

