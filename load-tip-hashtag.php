<?php
$tag = htmlspecialchars(@$_GET['keyword']);
echo "This is detail of <a href=\"hashtag.php\" target=\"_blank\">#$tag</a>";
?>