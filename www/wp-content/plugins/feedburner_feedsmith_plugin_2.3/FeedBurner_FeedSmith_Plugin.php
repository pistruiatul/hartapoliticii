<?php
/*
Plugin Name: FeedBurner FeedSmith
Plugin URI: http://www.feedburner.com/fb/a/help/wordpress_quickstart
Description: Originally authored by <a href="http://www.orderedlist.com/">Steve Smith</a>, this plugin detects all ways to access your original WordPress feeds and redirects them to your FeedBurner feed so you can track every possible subscriber. 
Author: FeedBurner
Author URI: http://www.feedburner.com
Version: 2.3.1
*/

$data = array(
	'feedburner_url'		=> '',
	'feedburner_comments_url'	=> ''
);

$ol_flash = '';

function ol_is_authorized() {
	global $user_level;
	if (function_exists("current_user_can")) {
		return current_user_can('activate_plugins');
	} else {
		return $user_level > 5;
	}
}
								
add_option('feedburner_settings',$data,'FeedBurner Feed Replacement Options');

$feedburner_settings = get_option('feedburner_settings');

function fb_is_hash_valid($form_hash) {
	$ret = false;
	$saved_hash = fb_retrieve_hash();
	if ($form_hash === $saved_hash) {
		$ret = true;
	}
	return $ret;
}

function fb_generate_hash() {
	return md5(uniqid(rand(), TRUE));
}

function fb_store_hash($generated_hash) {
	return update_option('feedsmith_token',$generated_hash,'FeedSmith Security Hash');
}

function fb_retrieve_hash() {
	$ret = get_option('feedsmith_token');
	return $ret;
}

function ol_add_feedburner_options_page() {
	if (function_exists('add_options_page')) {
		add_options_page('FeedBurner', 'FeedBurner', 8, basename(__FILE__), 'ol_feedburner_options_subpanel');
	}
}

function ol_feedburner_options_subpanel() {
	global $ol_flash, $feedburner_settings, $_POST, $wp_rewrite;
	if (ol_is_authorized()) {
		// Easiest test to see if we have been submitted to
		if(isset($_POST['feedburner_url']) || isset($_POST['feedburner_comments_url'])) {
			// Now we check the hash, to make sure we are not getting CSRF
			if(fb_is_hash_valid($_POST['token'])) {
				if (isset($_POST['feedburner_url'])) { 
					$feedburner_settings['feedburner_url'] = $_POST['feedburner_url'];
					update_option('feedburner_settings',$feedburner_settings);
					$ol_flash = "Your settings have been saved.";
				}
				if (isset($_POST['feedburner_comments_url'])) { 
					$feedburner_settings['feedburner_comments_url'] = $_POST['feedburner_comments_url'];
					update_option('feedburner_settings',$feedburner_settings);
					$ol_flash = "Your settings have been saved.";
				} 
			} else {
				// Invalid form hash, possible CSRF attempt
				$ol_flash = "Security hash missing.";
			} // endif fb_is_hash_valid
		} // endif isset(feedburner_url)
	} else {
		$ol_flash = "You don't have enough access rights.";
	}
	
	if ($ol_flash != '') echo '<div id="message" class="updated fade"><p>' . $ol_flash . '</p></div>';
	
	if (ol_is_authorized()) {
		$temp_hash = fb_generate_hash();
		fb_store_hash($temp_hash);
		echo '<div class="wrap">';
		echo '<h2>Set Up Your FeedBurner Feed</h2>';
		echo '<p>This plugin makes it easy to redirect 100% of traffic for your feeds to a FeedBurner feed you have created. FeedBurner can then track all of your feed subscriber traffic and usage and apply a variety of features you choose to improve and enhance your original WordPress feed.</p>
		<form action="" method="post">
		<input type="hidden" name="redirect" value="true" />
		<input type="hidden" name="token" value="' . fb_retrieve_hash() . '" />
		<ol>
		<li>To get started, <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . '" target="_blank">create a FeedBurner feed for ' . get_bloginfo('name') . '</a>. This feed will handle all traffic for your posts.</li>
		<li>Once you have created your FeedBurner feed, enter its address into the field below (http://feeds.feedburner.com/yourfeed):<br/><input type="text" name="feedburner_url" value="' . htmlentities($feedburner_settings['feedburner_url']) . '" size="45" /></li>
		<li>Optional: If you also want to handle your WordPress comments feed using FeedBurner, <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . '/wp-commentsrss2.php" target="_blank">create a FeedBurner comments feed</a> and then enter its address below:<br/><input type="text" name="feedburner_comments_url" value="' . htmlentities($feedburner_settings['feedburner_comments_url']) . '" size="45" />
		</ol>
		<p><input type="submit" value="Save" /></p></form>';
		echo '</div>';
	} else {
		echo '<div class="wrap"><p>Sorry, you are not allowed to access this page.</p></div>';
	}

}

function ol_feed_redirect() {
	global $wp, $feedburner_settings, $feed, $withcomments;
	if (is_feed() && $feed != 'comments-rss2' && !is_single() && $wp->query_vars['category_name'] == '' && ($withcomments != 1) && trim($feedburner_settings['feedburner_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	} elseif (is_feed() && ($feed == 'comments-rss2' || $withcomments == 1) && trim($feedburner_settings['feedburner_comments_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	}
}

function ol_check_url() {
	global $feedburner_settings;
	switch (basename($_SERVER['PHP_SELF'])) {
		case 'wp-rss.php':
		case 'wp-rss2.php':
		case 'wp-atom.php':
		case 'wp-rdf.php':
			if (trim($feedburner_settings['feedburner_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
		case 'wp-commentsrss2.php':
			if (trim($feedburner_settings['feedburner_comments_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
	}
}

if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
	add_action('template_redirect', 'ol_feed_redirect');
	add_action('init','ol_check_url');
}

add_action('admin_menu', 'ol_add_feedburner_options_page');

?>
