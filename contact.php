<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'vendor/autoload.php';

$parsedown = new Parsedown();

// 您可以在这里添加特定于联系页面的PHP代码，例如处理表单提交

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="styles/styles/atom-one-dark.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    <main class="container">
        <h1>Contact Me</h1>
        <section class="contact-info">
            <h2>My Introduction</h2>
            <p>Hello, I'm [Your Name], welcome to my personal page. Here you can find my projects and ways to get in touch with me.</p>
            
            <h2>Contact Details</h2>
            <ul>
                <li>Discord: w4r3s2022</li>
                <li>Github: <a href="https://github.com/w4r3s" target="_blank">https://github.com/w4r3s</a></li>
            </ul>
        </section>
    </main>
    <?php include 'footer.php'; ?>
   
    <script src="styles/highlight.min.js"></script>
    <script>console.log('Contact page loaded');</script>
    <script>hljs.highlightAll();</script>
</body>
</html>
