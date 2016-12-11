<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planetbiru Editor</title>
<style type="text/css">
body{
	font-family:Tahoma, Geneva, sans-serif;
	font-size:12px;
	color:#222222;
}
h2{
	font-size:24px;
	font-weight:normal;
}
a{
	color:#0084F3;
	text-decoration:none;
}
.dropdown-menu-sw::before {
    top: -16px;
    left: 10px;
    right: auto;
}
.dropdown-menu::before {
    border: 8px solid transparent;
    border-bottom-color: #DDDDDD;
}
.dropdown-menu::before, .dropdown-menu::after {
    position: absolute;
    display: inline-block;
    content: "";
}
.dropdown-menu::after {
    top: -14px;
    left: 11px;
    border: 7px solid transparent;
    border-bottom-color: #FCFCFC;
}
.dropdown-menu {
    width: 180px;
    margin-top: 13px;
}
.dropdown-menu-sw {
    right: 0;
    left: auto;
}
.dropdown-menu {
    position: absolute;
    left: 10px;
    z-index: 100;
    padding-top: 5px;
    padding-bottom: 5px;
    margin-top: 2px;
    background-color: #FCFCFC;
    background-clip: padding-box;
    border: 1px solid #DDDDDD;
    border-radius: 4px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.15);
	padding:10px 10px;
	min-height:60px;
	min-width:260px;
	display:none;
}
.close-button {
    float: right;
    margin-top: 2px;
	margin-right:-2px;
    height: 16px;
    overflow: hidden;
    width: 16px;
    display: inline-block;
	margin-left:10px;
}
.close-button a {
    text-decoration: none;
    color: #333333;
    font-size: 20px;
    line-height: 0.6;
    padding: 0;
    margin-top: -2px;
    position: absolute;
}
.close-button a:hover {
	color:#006BB0;
}
textarea{
	width:415px; 
	height:200px; 
	padding:8px;
	border:1px solid #DDDDDD;
	background-color:#FFFFFF;
}
.footer{
	padding:20px 0;
}
</style>
</head>

<body>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery.caretposition.js"></script>
<h2>Planetbiru Editor</h2>
<textarea name="textarea" rows="3" id="textarea" style="" spellcheck="false">Username saya adalah @Kamshory. Silakan follow @Kamshory. Atau Anda juga bisa ketik #Kamshory. @Kamshory @masroy @roy @mas @planetbiru. 
Kunjungi profil saya di http://www.planetbiru.com/kamshory</textarea>
<script type="text/javascript">
function getFirstEmail(email) {
	try{
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if(re.test(email))
		{
			return email;
		}
		else
		{
			return "";
		}
	}
	catch(e)
	{
		return "";
	}
	return "";
}
function getFirstURL(string) {
	try{
		var pattern = /(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/;
		var match = string.match(pattern);
		return match[0];
	}
	catch(e){
		return "";
	}
	return "";
}
function getTagInfo(tag)
{
	if(tag == '#') return '';

	//tag = '#'+tag.split(/\s*\b\s*/)[1];	
	tag = '#'+tag.replace(/[^A-Za-z0-9^_]/g," ").trim().split(" ")[0];
	
	var url = 'load-tip-hashtag.php';
	return {html:'<div class="tip-dynamic-hashtag">Search hashtag <a href="tags/'+tag.substr(1)+'">'+tag+'</a></div>', selector:'.tip-dynamic-hashtag', keyword:tag.substr(1), url:url};
}
function getUserInfo(user)
{
	if(user == '@') return '';

	//user = '@'+user.split(/\s*\b\s*/)[1];	
	user = '@'+user.replace(/[^A-Za-z0-9^_]/g," ").trim().split(" ")[0];
	
	var url = 'load-tip-user.php';
	return {html:'<div class="tip-dynamic-user">Loading info of <a href="user/'+user.substr(1)+'">'+user+'</a></div>', selector:'.tip-dynamic-user', keyword:user.substr(1), url:url};
}
function getInfo(info)
{
	info = info.split("\\s+|(?=\\W\\p{Punct}|\\p{Punct}\\W)|(?<=\\W\\p{Punct}|\\p{Punct}\\W})")[0];
	
	if(info.charAt(0) == '@')
	{
		return getUserInfo(info);
	}
	if(info.charAt(0) == '#')
	{
		return getTagInfo(info);
	}
}
var locked = false;
var lastSelectedWord = '';
$(function() {
	var tip = $('.dropdown-menu');
	$(document).on('keyup click focus', '#textarea', function(e) {
		var info = $(this).getControlStatus();
		var firstEmail = getFirstEmail(info.selectedWord);
		var firstURL = getFirstURL(info.selectedWord);
		if(info.selectedWord.charAt(0) == '@' || info.selectedWord.charAt(0) == '#' )
		{
			var pos = $(this).getCaretPosition();
			if(info.selectedWord.length > 1)
			{
				if(lastSelectedWord != info.selectedWord)
				{
					var content = getInfo(info.selectedWord);
					tip.find('.info-content').html(content.html);
					
					$.get(content.url, {keyword:content.keyword}, function(answer){
						$(content.selector).empty().append(answer);
					});
					
				}
				
				tip.css({
					left: this.offsetLeft + pos.left - 18,
					top: this.offsetTop + pos.top + 22
				}).show();
			}
			else
			{
				tip.hide();
			}
		}
		else if(firstEmail.length > 0)
		{
			if(lastSelectedWord != info.selectedWord)
			{
				var pos = $(this).getCaretPosition();
				var linkURL = 'mailto:'+info.selectedWord;
				tip.find('.info-content').html('Send mail to <a href="'+linkURL+'">'+info.selectedWord+'</a>');
				tip.css({
					left: this.offsetLeft + pos.left - 18,
					top: this.offsetTop + pos.top + 22
				}).show();
			}
		}
		else if(firstURL.length > 0)
		{
			if(lastSelectedWord != info.selectedWord)
			{
				var pos = $(this).getCaretPosition();
				var linkURL = info.selectedWord;
				if(info.selectedWord.indexOf('://') == -1)
				{
					linkURL = 'http://'+info.selectedWord;
				}
				tip.find('.info-content').html('Open URL <a href="'+linkURL+'" target="_blank">'+info.selectedWord+'</a>');
				tip.css({
					left: this.offsetLeft + pos.left - 18,
					top: this.offsetTop + pos.top + 22
				}).show();
			}
		}
		else
		{
			tip.hide();
		}
		lastSelectedWord = info.selectedWord;
		locked = false;
		$('#status').text(JSON.stringify(info));
	})
	$(document).on('blur', '#textarea', function(e){
		if(!locked)
		{
			tip.hide();
		}
	});
	$(document).on('mouseenter', '.dropdown-menu', function(e){
		locked = true;
	});
	$(document).on('mouseleave', '.dropdown-menu', function(e){
		locked = false;
	});
	$(document).on('click', '.close-tip', function(e){
		tip.fadeOut(200);
		e.preventDefault();
	});
	
});
</script>

<div class="dropdown-menu dropdown-menu-sw">
	<span class="close-button"><a href="#" class="close-tip">×</a></span>
	<div class="info-content"></div>
</div>
      
<div id="status">&nbsp;</div>

<div class="footer">Join <a href="http://www.planetbiru.com">www.planetbiru.com</a></div>
</body>
</html>
