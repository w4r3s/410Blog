<?php
require_once 'config.php';
require_once 'connect.php';

header('Content-Type: application/json; charset=UTF-8');

$jsonfeed = array();
$jsonfeed['version'] = 'https://jsonfeed.org/version/1';
$jsonfeed['title'] = BLOG_TITLE;
$jsonfeed['home_page_url'] = 'http://yourwebsite.com';
$jsonfeed['feed_url'] = 'http://yourwebsite.com/jsonfeed.json';
$jsonfeed['items'] = array();

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $item = array();
    $item['id'] = $row['post_id'];
    $item['title'] = $row['title'];
    $item['content_html'] = htmlspecialchars($row['content']);
    $item['url'] = 'http://yourwebsite.com/article-link.php?post_id=' . $row['post_id'];
    $item['date_published'] = date(DATE_ISO8601, strtotime($row['created_at']));
    
    array_push($jsonfeed['items'], $item);
}

echo json_encode($jsonfeed, JSON_PRETTY_PRINT);
?>
