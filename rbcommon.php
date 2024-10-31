<?php

include("lastRSS.php");

// get rbarticle properties
$rbarticle_title_maxlength = get_option('rbarticle_max_title_length');
$rbarticle_separator = get_option('rbarticle_separator');
$rbarticle_refresh = get_option('rbarticle_update_time')*1000; // get microseconds
$rbarticle_cache_time = get_option('rbarticle_cache_time');
$rbarticle_feeds = array_values(get_option('rbarticle_feed_list'));

function rbarticle_display_article($status = false){

	global $rbarticle_separator;

	$article = rbarticle_get_article();
	if(!isset($article['error']))
		return (($status)? '1' : '') . "<span class=\"blog\">" . $article['blog'] . "</span><span class=\"seperator\">" . $rbarticle_separator ."</span><a href=\"" . $article['link'] . "\" target=\"_new\">" . $article['title'] . "</a>";
	else
		return (($status)? '0' : '') . $article['error'];

}

function rbarticle_get_article(){

	global $rbarticle_feeds, $rbarticle_title_maxlength;
	
	// choose a feed
	$totalFeeds = count($rbarticle_feeds);
	$feedId = rand(0, $totalFeeds-1);
	$feedUrl = $rbarticle_feeds[$feedId]['url'];
	$feedTitle = $rbarticle_feeds[$feedId]['title'];
	
	// get the feed into an array
 	$rss = new lastRSS; 
  	// setup transparent cache
  	$rss->cache_dir = dirname(__FILE__) . '/cache'; 
  	$rss->cache_time = $rbarticle_cache_time;
  	// load the RSS file
  	if ($rs = $rss->get($feedUrl)) {
  		$totalArticles = $rs['items_count'];
			$articleId = rand(0, $totalArticles-1);
			$article = $rs['items'][$articleId];
			$article['blog'] = $feedTitle; // append blog title
			if($rbarticle_title_maxlength AND strlen($article['title']) > $rbarticle_title_maxlength) $article['title'] = substr(html_entity_decode($article['title']), 0, $rbarticle_title_maxlength) . '...';

			return $article;
  	}
	
	$article['error'] = $feedTitle . ' could not get feed.';
	return $article;

}

?>