<?php 
require_once 'config.php';
$lang = require __DIR__ . '/lang/lang_' . CURRENT_LANG . '.php';
?>

<nav>
    <ul>
        <li><a href="index.php"><?php echo $lang['nav_me']; ?></a></li>
        <li><a href="blog.php"><?php echo $lang['nav_blog']; ?></a></li>
        <li><a href="contact.php"><?php echo $lang['nav_contact']; ?></a></li>
        <li><a href="share.php"><?php echo $lang['nav_share']; ?></a></li>
        <li><a href="files.php"><?php echo $lang['nav_files']; ?></a></li>
        <li><a href="https://hashb.in/"><?php echo $lang['nav_paste']; ?></a></li>
        <li><a href="friends.php"><?php echo $lang['nav_friends']; ?></a></li>
    </ul>
</nav>
