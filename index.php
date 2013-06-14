<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8">
	<title>DEMO - Twitter API PHP Cache</title>

<style type="text/css">

/* http://meyerweb.com/eric/tools/css/reset/ 
   v2.0 | 20110126
   License: none (public domain)
*/

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	background-color: #fff;
	font-family: Arial, sans-serif;
	font-size: 100%; /* 16px */
	line-height: 1.625em;
	font-weight: 400;
	color: #444;
}
.demo {
	padding: 100px 40px;
	max-width: 620px;
	margin: 0 auto;
}
.demo li {
	margin-bottom: 1em;
}

</style>


</head>
<body>


<div class="demo">
<?php 


require_once('twitter.php');

$twitter = new Twitter();
$tweets = $twitter->getTweets();



if(is_array($tweets))
{
    echo '<ul class="tweets">';
    foreach($tweets as $tweet) {
        $text = $tweet['text'];
        $text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\">@\\2</a>'", $text);
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\">\\2</a>'", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\">\\2</a>'", $text);
        $text = preg_replace('/([^a-zA-Z0-9-_&])#([0-9a-zA-Z_]+)/',"$1<a href=\"http://twitter.com/search?q=%23$2\">#$2</a>",$text);
        echo '<li>'.$text.'</li>';
    }
    echo '</ul>';
}
else
{
    echo '<p>' . $tweets . '</p>';  
}


?>

</div>

</body>
</html>