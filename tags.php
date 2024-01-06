<?php
require_once 'config.php';
require_once 'connect.php';

// 获取URL参数中的标签名称
$tagName = isset($_GET['tag']) ? $_GET['tag'] : '';

// 获取所有标签
$allTagsStmt = $pdo->query("SELECT name FROM tags");
$allTags = $allTagsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts Tagged: <?php echo htmlspecialchars($tagName); ?> - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="blog-articles">
        <h2>Posts Tagged: <?php echo htmlspecialchars($tagName); ?></h2>
        
        <?php
        // 查询与标签关联的博文
        $stmt = $pdo->prepare("SELECT p.* FROM posts p INNER JOIN post_tags pt ON p.post_id = pt.post_id INNER JOIN tags t ON pt.tag_id = t.tag_id WHERE t.name = ?");
        $stmt->execute([$tagName]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = date('d/m/Y', strtotime($row['created_at']));
            echo "<article>";
            echo "<h3><a href='article-link.php?post_id={$row['post_id']}'>{$date} - " . htmlspecialchars($row['title']) . "</a></h3>";
            echo "</article>";
        }
        ?>

        <!-- Tag Indicator -->
        <div class="tag-indicator">
            <h3>Tags:</h3>
            <?php
            foreach ($allTags as $tag) {
                echo "<a href='tags.php?tag=" . urlencode($tag['name']) . "'>" . htmlspecialchars($tag['name']) . "</a> ";
            }
            ?>
        </div>
    </main>
    
</body>
</html>
