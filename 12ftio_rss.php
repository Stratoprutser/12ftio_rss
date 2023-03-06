<?php
require_once('include/simplepie/autoloader.php');

if(isset($_GET["url"])) $url=$_GET["url"]; else $url=NULL;

if (trim($url)=="")
{
?>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<form action="12ftio_rss.php">
	<input name="url" placeholder="https://wordpress.org/feed"><br>
	<input type="submit"></form>
	<?php
	die();
}

$rss = new SimplePie();
$rss->set_cache_location('/var/www/cache/simplepie');

$rss->set_feed_url($url);
$success = $rss->init();

header('Content-Type: text/xml; charset=UTF-8');

?>
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"	>
	<channel>
		<title>12ft.io - <?php print $rss->get_title(); ?></title>
		<atom:link href="<?php print 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" rel="self" type="application/rss+xml" />
		<link><?php print 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></link>
		<description>12ftio: <?php print clean_up($rss->get_description()); ?></description>
<?php if (null!==$rss->get_language() && $rss->get_language() !=="")
		print "\t\t<language>". $rss->get_language()."</language>\n"; ?>
		<lastBuildDate><?php print date('r'); ?></lastBuildDate>
<?php

foreach ($rss->get_items() as $item)

{
	print "<item>\n";
	$href = $item->get_permalink();

	$title = $item->get_title();

	if ($item->get_id()!=="") $guid = $item->get_id(); else $guid=$item->get_permalink();

	print "\t<title>".$title."</title>\n";
	print "\t<guid isPermaLink=\"false\">".$guid."</guid>\n";
	print "\t<link>https://12ft.io/".$href."</link>\n";
	print "\t<description>".trim(preg_replace('/\s+/', ' ', clean_up(strip_tags($item->get_description()))))."</description>\n";
	print "\t<pubDate>".$item->get_date('r')."</pubDate>\n";
	print "</item>\n";
}

function clean_up($subject) 
{
	$subject=str_replace("&","&#x26;",$subject);
	$subject=str_replace(">","&gt;",$subject);
	$subject=str_replace("<","&#x3C;",$subject);
	$subject=str_replace(chr(8),"",$subject);
	return $subject;
}
?></channel></rss>
