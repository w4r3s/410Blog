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
$publicDir = '../public';
$staticDir = $publicDir . '/statics';
$blogDir = $publicDir . '/blog';

// 创建所需的文件夹
if (!is_dir($staticDir)) {
    mkdir($staticDir, 0777, true);
}
if (!is_dir($blogDir)) {
    mkdir($blogDir, 0777, true);
}

function includeAndGetContent($filePath, $basePath = '', $isBlogPage = false) {
    ob_start();
    include $filePath;
    $content = ob_get_clean();
    // 修改链接，确保指向正确的路径
    if ($isBlogPage) {
        $content = str_replace(['href="index.php"', 'href="blog.php"'], ['href="../index.html"', 'href="../blog.html"'], $content);
    } else {
        $content = str_replace(['href="index.php"', 'href="blog.php"'], ['href="index.html"', 'href="blog.html"'], $content);
    }
    $content = str_replace(['src="', 'href="'], ['src="' . $basePath, 'href="' . $basePath], $content);
    return $content;
}

$menu = includeAndGetContent('../menu.php');
$footer = includeAndGetContent('../footer.php', 'statics/');

// 生成首页的静态页面
$stmt = $pdo->prepare("SELECT content FROM homepage WHERE id = 1");
$stmt->execute();
$homepage = $stmt->fetch(PDO::FETCH_ASSOC);
$homepageContent = $homepage ? $parsedown->text($homepage['content']) : '';

$indexHtml = "<!DOCTYPE html>\n";
$indexHtml .= "<html lang='en'>\n<head>\n";
$indexHtml .= "<link rel='icon' href='statics/favicon.ico' type='image/x-icon'>\n";
$indexHtml .= "<meta charset='UTF-8'>\n";
$indexHtml .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
$indexHtml .= "<title>" . BLOG_TITLE . "</title>\n";
$indexHtml .= "<link rel='stylesheet' type='text/css' href='statics/styles.css'>\n";
$indexHtml .= "</head>\n<body>\n";
$indexHtml .= $menu;
$indexHtml .= "<main>\n" . $homepageContent . "\n</main>\n";
$indexHtml .= $footer;
$indexHtml .= "</body>\n</html>";

file_put_contents($publicDir . '/index.html', $indexHtml);

// 生成博客列表的静态页面
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$blogHtml = "<!DOCTYPE html>\n";
$blogHtml .= "<html lang='en'>\n<head>\n";
$blogHtml .= "<meta charset='UTF-8'>\n";
$blogHtml .= "<title>Blog Articles - " . BLOG_TITLE . "</title>\n";
$blogHtml .= "<link rel='stylesheet' type='text/css' href='statics/styles.css'>\n";
$blogHtml .= "</head>\n<body>\n";
$blogHtml .= $menu;
$blogHtml .= "<main class='blog-articles'>\n<h2>Blog Articles</h2>\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = date('d/m/Y', strtotime($row['created_at']));
    $blogHtml .= "<article>";
    $blogHtml .= "<h3><a href='blog/article-" . $row['post_id'] . ".html'>" . $date . " - " . htmlspecialchars($row['title']) . "</a></h3>";
    $blogHtml .= "</article>";
}

$blogHtml .= "</main>\n";
$blogHtml .= $footer;
$blogHtml .= "</body>\n</html>";

file_put_contents($publicDir . '/blog.html', $blogHtml);

// 生成单个博客文章的静态页面
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $title = htmlspecialchars($row['title']);
    $content = $parsedown->text($row['content']);
    $articleFileName = $blogDir . '/article-' . $row['post_id'] . '.html';

    $articleHtml = "<!DOCTYPE html>\n";
    $articleHtml .= "<html lang='en'>\n<head>\n";
    $articleHtml .= "<meta charset='UTF-8'>\n";
    $articleHtml .= "<title>$title - " . BLOG_TITLE . "</title>\n";
    $articleHtml .= "<link rel='stylesheet' type='text/css' href='../statics/styles.css'>\n";
    $articleHtml .= "</head>\n<body>\n";
    $articleHtml .= includeAndGetContent('../menu.php', './', true); // 修改此行以移除 'statics/' 前缀
    $articleHtml .= "<main>\n";
    $articleHtml .= "<article>\n<h1>$title</h1>\n";
    $articleHtml .= "<div class='post-content'>$content</div>\n";
    $articleHtml .= "</article>\n";
    $articleHtml .= "</main>\n";
    $articleHtml .= $footer;
    $articleHtml .= "</body>\n</html>";

    file_put_contents($articleFileName, $articleHtml);
}



// 复制静态资源
$staticFiles = ['styles.css', 'favicon.ico', 'fonts'];
foreach ($staticFiles as $file) {
    if (is_dir("../$file")) {
        $dest = $staticDir . '/' . basename($file);
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../$file")) as $filename) {
            if (!is_file($filename)) continue;
            $destFilename = $dest . '/' . basename($filename);
            copy($filename, $destFilename);
        }
    } else {
        copy("../$file", $staticDir . '/' . $file);
    }
}

echo "Static site generation complete.";
?>
