<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ShareLinks = [
    ['name' => 'OpenAI', 'url' => 'https://www.openai.com'],
    ['name' => 'GitHub', 'url' => 'https://github.com'],
    ['name' => 'Stack Overflow', 'url' => 'https://stackoverflow.com'],
    
];

$parsedown = new Parsedown();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles/styles/atom-one-dark.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <main class="blog-articles">
        <h1>Share</h1>
        <ul>
            <?php foreach ($ShareLinks as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
