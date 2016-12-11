<?php
$tag = htmlspecialchars(@$_GET['keyword']);

if(strtolower($tag) == "masroy")
echo "There are 2875 posts with hashtag <a href=\"hashtag/$tag\" target=\"_blank\">#$tag</a>";

else if(strtolower($tag) == "kamshory")
echo "There are 542 posts with hashtag <a href=\"hashtag/$tag\" target=\"_blank\">#$tag</a>";

else
echo "Searching post with hashtag <a href=\"hashtag/$tag\" target=\"_blank\">#$tag</a>";


?>