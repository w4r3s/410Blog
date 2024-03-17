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

// 生成RSS Feed
$rssFileName = $publicDir . '/blog.rss'; // RSS文件将被保存在静态页面的根目录
$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
$rssfeed .= '<rss version="2.0">';
$rssfeed .= '<channel>';
$rssfeed .= '<title>' . BLOG_TITLE . '</title>';
$rssfeed .= '<link>' . BLOG_URL . '</link>';
$rssfeed .= '<description>Your blog description here</description>';
$rssfeed .= '<language>en-us</language>';

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rssfeed .= '<item>';
    $rssfeed .= '<title>' . htmlspecialchars($row['title']) . '</title>';
    $rssfeed .= '<link>' . BLOG_URL . '/blog/article-' . $row['post_id'] . '.html' . '</link>'; // 修改链接指向静态文章
    $rssfeed .= '<description>' . htmlspecialchars($row['content']) . '</description>';
    $rssfeed .= '<pubDate>' . date(DATE_RSS, strtotime($row['created_at'])) . '</pubDate>';
    $rssfeed .= '</item>';
}

$rssfeed .= '</channel>';
$rssfeed .= '</rss>';

file_put_contents($rssFileName, $rssfeed);

// 生成JSON Feed
$jsonFileName = $publicDir . '/blog.json'; // JSON文件将被保存在静态页面的根目录
$jsonfeed = array();
$jsonfeed['version'] = 'https://jsonfeed.org/version/1';
$jsonfeed['title'] = BLOG_TITLE;
$jsonfeed['home_page_url'] = BLOG_URL;
$jsonfeed['feed_url'] = BLOG_URL . '/blog.json';
$jsonfeed['items'] = array();

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $item = array();
    $item['id'] = $row['post_id'];
    $item['title'] = $row['title'];
    $item['content_html'] = htmlspecialchars($row['content']);
    $item['url'] = BLOG_URL . '/blog/article-' . $row['post_id'] . '.html'; // 修改链接指向静态文章
    $item['date_published'] = date(DATE_ISO8601, strtotime($row['created_at']));
    
    array_push($jsonfeed['items'], $item);
}

file_put_contents($jsonFileName, json_encode($jsonfeed, JSON_PRETTY_PRINT));


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
$indexHtml .= "<link rel='stylesheet' href='statics/styles/atom-one-dark.css'>\n"; // 确保路径正确
// 添加控制图片大小的CSS
$indexHtml .= "<style>\n";
$indexHtml .= "main img {\n";
$indexHtml .= "  max-width: 960px;\n";
$indexHtml .= "  max-height: 400px;\n";
$indexHtml .= "  width: auto; /* 保持图片的原始宽高比 */\n";
$indexHtml .= "  height: auto; /* 保持图片的原始宽高比 */\n";
$indexHtml .= "}\n";
$indexHtml .= "</style>\n";
$indexHtml .= "</head>\n<body>\n";
$indexHtml .= $menu; // 确保$menu已正确获取
$indexHtml .= "<main>\n" . $homepageContent . "\n</main>\n"; // 确保$homepageContent已正确获取
$indexHtml .= $footer; // 确保$footer已正确获取
$indexHtml .= "<script src='statics/highlight.min.js'></script>\n"; // 确保路径正确
$indexHtml .= "<script>hljs.highlightAll();</script>\n";
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
//订阅
$blogHtml .= "<p>If you have a compatible reader, be sure to check out my <a href='./blog.rss'>RSS feed</a> for automatic updates. Also check out the <a href='./blog.json'>JSONFeed</a>.</p>\n";

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

    // 计算发布日期、字数和预计阅读时间
    $publishedDate = date('d/m/Y', strtotime($row['created_at']));
    $wordCount = str_word_count(strip_tags($row['content']));
    $readingTime = ceil($wordCount / 200); // 假设平均阅读速度为200字/分钟

    $articleHtml = "<!DOCTYPE html>\n";
    $articleHtml .= "<html lang='en'>\n<head>\n";
    $articleHtml .= "<meta charset='UTF-8'>\n";
    $articleHtml .= "<title>$title - " . BLOG_TITLE . "</title>\n";
    $articleHtml .= "<link rel='stylesheet' type='text/css' href='../statics/styles.css'>\n";
    $articleHtml .= "<link rel='stylesheet' href='../statics/styles/atom-one-dark.css'>\n"; // 确保路径正确
    $articleHtml .= "<style>\n";
    $articleHtml .= "img {\n";
    $articleHtml .= "  max-width: 960px;\n";
    $articleHtml .= "  max-height: 400px;\n";
    $articleHtml .= "  width: auto; /* 保持图片的原始宽高比 */\n";
    $articleHtml .= "  height: auto; /* 保持图片的原始宽高比 */\n";
    $articleHtml .= "}\n";
    $articleHtml .= "</style>\n";
    $articleHtml .= "</head>\n<body>\n";
    $articleHtml .= includeAndGetContent('../menu.php', '../', true);
    $articleHtml .= "<main>\n";
    $articleHtml .= "<article>\n<h1>$title</h1>\n";
    $articleHtml .= "<p style='font-size: 14px; color: gray;'>Published on $publishedDate, $wordCount words, $readingTime minute" . ($readingTime == 1 ? "" : "s") . " to read</p>\n";
    $articleHtml .= "<div class='post-content'>$content</div>\n";
    $articleHtml .= "</article>\n";
    $articleHtml .= "</main>\n";
    $articleHtml .= includeAndGetContent('../footer.php', '../');
    $articleHtml .= "<script src='../statics/highlight.min.js'></script>\n"; // 确保路径正确
    $articleHtml .= "<script>hljs.highlightAll();</script>\n";
    $articleHtml .= "</body>\n</html>";

    file_put_contents($articleFileName, $articleHtml);
}




// 复制静态资源
$staticFiles = ['styles.css', 'favicon.ico', 'fonts', 'styles/highlight.min.js', 'styles/styles/atom-one-dark.css'];
foreach ($staticFiles as $file) {
    $source = "../$file"; // 原始路径
    // 特别处理目标路径以反映正确的目标文件结构
    if (strpos($file, 'styles/highlight.min.js') !== false) {
        $destination = $staticDir . '/highlight.min.js'; // 将JS文件复制到statics目录
    } elseif (strpos($file, 'styles/styles/atom-one-dark.css') !== false) {
        $destination = $staticDir . '/styles/atom-one-dark.css'; // 将CSS文件复制到statics/styles目录
    } else {
        $destination = $staticDir . '/' . $file; // 其他文件保持原有的复制逻辑
    }

    // 检查是否是目录，若是，则递归复制目录
    if (is_dir($source)) {
        $dest = $staticDir . '/' . basename($file);
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source)) as $filename) {
            if (!is_file($filename)) continue;
            $destFilename = $dest . '/' . str_replace($source . '/', '', $filename);
            $destDir = dirname($destFilename);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            copy($filename, $destFilename);
        }
    } else {
        // 不是目录，直接复制文件
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        copy($source, $destination);
    }
}



echo "Static site generation complete.";
?>
