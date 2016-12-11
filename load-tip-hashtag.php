<?php
$tag = htmlspecialchars(@$_GET['keyword']);
echo "This is detail of <a href=\"hashtag/$tag\" target=\"_blank\">#$tag</a>";
?>