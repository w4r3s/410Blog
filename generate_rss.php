<?php
require_once 'config.php';
require_once 'connect.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
$rssfeed .= '<rss version="2.0">';
$rssfeed .= '<channel>';
$rssfeed .= '<title>' . BLOG_TITLE . '</title>';
$rssfeed .= '<link>http://yourwebsite.com</link>';
$rssfeed .= '<description>Your blog description here</description>';
$rssfeed .= '<language>en-us</language>';

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rssfeed .= '<item>';
    $rssfeed .= '<title>' . htmlspecialchars($row['title']) . '</title>';
    $rssfeed .= '<link>http://yourwebsite.com/article-link.php?post_id=' . $row['post_id'] . '</link>';
    $rssfeed .= '<description>' . htmlspecialchars($row['content']) . '</description>';
    $rssfeed .= '<pubDate>' . date(DATE_RSS, strtotime($row['created_at'])) . '</pubDate>';
    $rssfeed .= '</item>';
}

$rssfeed .= '</channel>';
$rssfeed .= '</rss>';

echo $rssfeed;
?>
