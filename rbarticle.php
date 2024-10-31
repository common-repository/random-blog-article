<?php

/*
Plugin Name: Random Blog Article
Plugin URI: http://www.randomblog.com/
Description: Plugin to place a random article from selected RSS feeds on to page with AJAX update.
Author: Random Blog
Version: 1.01
Author URI: http://www.randomblog.com/
*/

include("rbcommon.php");

// set up ajax call
add_action('wp_head', 'rbarticle_js_header' );
// add a menu item
add_action('admin_menu', 'rbarticle_add_pages');

// add default values if not set
add_option('rbarticle_update_time', '10' ); // 10 seconds
add_option('rbarticle_max_title_length', '30' ); // 30 letters
add_option('rbarticle_separator', ' - ' );
add_option('rbarticle_cache_time', '3600'); // 1 hour
add_option('rbarticle_feed_list', array(array('title' => 'Blog Random', 'url' => 'http://www.blograndom.com/blog/feed/'))); // cheap plug

function rbarticle_add_pages(){
	
	add_options_page('Random Blog Article', 'RB Article', 8, __FILE__, 'rbarticle_admin_page');
	
}

function rbarticle_js_header(){

	global $rbarticle_refresh;

  // use JavaScript SACK library for AJAX
  wp_print_scripts( array( 'sack' ));

?>
<script type="text/javascript">
//<![CDATA[
function rbarticle_update_article( results_div ){
 var mysack = new sack( "<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/rbarticle/rbarticle_ajax.php" );

	mysack.execute = 1;
	mysack.method = 'POST';
	mysack.setVar( "results_div_id", results_div );
	mysack.onError = function() { alert('AJAX error in voting' )};
  	mysack.runAJAX();

	setTimeout("rbarticle_update_article('" + results_div + "')", <?php echo $rbarticle_refresh; ?>);

	return true;

}

//]]>
</script>
<?php
} // end of PHP function rbarticle_js_header

function rbarticle_tpl_init(){

	global $rbarticle_refresh;
	
	echo "<div id=\"rbarticle_result\">" . rbarticle_display_article() . "</div>";
	
	if($rbarticle_refresh){ ?> 
	<script type="text/javascript">
	//<![CDATA[
	setTimeout("rbarticle_update_article('rbarticle_result')", <?php echo $rbarticle_refresh; ?>); 
	//]]>
	</script>
	<?php }

}


function rbarticle_admin_page(){

	if( $_POST['rbarticle_submit'] == 'Y' ) {
    // Read their posted value
    $updateTime = $_POST['rbarticle_update_time'];
		$maxTitleLength = $_POST['rbarticle_max_title_length'];
		$separator = $_POST['rbarticle_separator'];
		$cacheTime = $_POST['rbarticle_cache_time'];
		$feedList = $_POST['rbarticle_feed_list'];
		
		// try to keep separator intact
		$separator = str_replace(" ", '&nbsp;', htmlentities($separator));
		
		// remove blanks from feedlist
		if(is_array($feedList)){
			foreach($feedList AS $feedId => $feed)
				if(empty($feed['url'])) unset($feedList[$feedId]);
		}else{ $feedList = array(); }

    // Save the posted value in the database
    update_option('rbarticle_update_time', $updateTime );
		update_option('rbarticle_max_title_length', $maxTitleLength );
		update_option('rbarticle_separator', $separator );
		update_option('rbarticle_cache_time', $cacheTime);
		update_option('rbarticle_feed_list', $feedList );

    // Put an options updated message on the screen
?>
<div class="updated"><p><strong>Settings updated.</strong></p></div>
<?php
    }else{
			$updateTime = get_option('rbarticle_update_time');
			$maxTitleLength = get_option('rbarticle_max_title_length');
			$separator = get_option('rbarticle_separator');
			$cacheTime = get_option('rbarticle_cache_time');
			$feedList = get_option('rbarticle_feed_list');
	}
?>

<script language="JavaScript" type="text/javascript">
<!--
var rbarticle_next_feed = <?php echo count($feedList)+2; ?>;

function rbarticle_add_feed(){

	var rbarticle_feed_ul = document.getElementById('rbarticle_feed_list');
	var newLI = document.createElement("li");
	newLI.innerHTML = 'Blog Title: <input type="text" name="rbarticle_feed_list[' + rbarticle_next_feed +'][title]" value="" size="20"> Blog URL: <input type="text" name="rbarticle_feed_list[' + rbarticle_next_feed +'][url]" value="" size="40"> <a href="javascript:;" onclick="rbarticle_remove_feed(this);">delete</a>';
	rbarticle_feed_ul.appendChild(newLI);
	
	rbarticle_next_feed = rbarticle_next_feed+1;

}

function rbarticle_remove_feed(feedli){

	if(confirm('Are you sure?')){
	
		feedli.parentNode.parentNode.removeChild(feedli.parentNode);
	
	}

}
//-->
</script>

<div class="wrap">
<h2>Random Blog Article Settings</h2>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="rbarticle_submit" value="Y">

<h3>General</h3>
<ul>
<li>Update article via ajax every <input type="text" name="rbarticle_update_time" value="<?php echo $updateTime; ?>" size="3"> seconds (0 = no update).</li>
<li>Limit an articles title length to <input type="text" name="rbarticle_max_title_length" value="<?php echo $maxTitleLength; ?>" size="3"> letters (0 = no limit).</li>
<li>Separate the blog title and article title by <input type="text" name="rbarticle_separator" value="<?php echo $separator; ?>" size="3">.</li>
<li>Cache RSS feeds for <input type="text" name="rbarticle_cache_time" value="<?php echo $cacheTime; ?>" size="4"> seconds.</li>
</ul>

<h3>Feed List</h3>
<ul id="rbarticle_feed_list">
<?php
if(is_array($feedList)) foreach($feedList AS $feedId => $feed){
?>
	<li>
		Blog Title: <input type="text" name="rbarticle_feed_list[<?php echo $feedId; ?>][title]" value="<?php echo $feed['title']; ?>" size="20">
		Blog URL: <input type="text" name="rbarticle_feed_list[<?php echo $feedId; ?>][url]" value="<?php echo $feed['url']; ?>" size="40">
		<a href="javascript:;" onclick="rbarticle_remove_feed(this);">delete</a>
	</li>
<?php } ?>
<li>
		Blog Title: <input type="text" name="rbarticle_feed_list[<?php echo $feedId+1; ?>][title]" value="" size="20">
		Blog URL: <input type="text" name="rbarticle_feed_list[<?php echo $feedId+1; ?>][url]" value="" size="40">
		<a href="javascript:;" onclick="rbarticle_remove_feed(this);">delete</a>
</li>
</ul>

<p><a href="javascript:;" onclick="rbarticle_add_feed();">Add another</a></p>

<p class="submit">
<input type="submit" name="Submit" value="Update Options" />
</p>

</form>
</div>
<?php
}

?>