<?php
$user = htmlspecialchars(@$_GET['keyword']);
$array_users = array("kamshory", "masroy", "mas", "roy", "planet", "biru", "planetbiru");
if(in_array(strtolower($user), $array_users))
{
	echo "This is detail of <a href=\"$user\" target=\"_blank\">@$user</a><p>This is a paragraph.</p><p>This is a paragraph.</p><p>This is a paragraph.</p><p>This is a paragraph.</p><p>This is a paragraph.</p>";
}
else
{
	echo "User $user is not found. <a href=\"claim.php?username=$user\" target=\"_blank\">Claim @$user as your ID</a>";
}
?>