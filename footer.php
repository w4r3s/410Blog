<?php
// footer.php
require_once 'config.php'; 
// 加载语言文件
$lang = require __DIR__ . '/lang/lang_' . CURRENT_LANG . '.php';
?>


<footer style="background-color: <?php echo BLOG_BACKGROUND_COLOR; ?>; color: black; font-family: <?php echo BLOG_FONT_PROSE; ?>; text-align: center; padding: 10px 0;">
    &copy; 2023-<?php echo date("Y"); ?> w4r3s.<br>
    <?php echo $lang['source_code_available']; ?><a href="https://github.com/w4r3s/410Blog" target="_blank"><?php echo $lang['here']; ?></a>
</footer>
