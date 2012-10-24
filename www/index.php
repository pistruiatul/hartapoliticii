<?php
// The database user and password are specified in this file. For development
// we've included a secret/db_user.php.sample that should be renamed and then
// edited to match your local development environment.
require_once('secret/db_user.php');

// Include the templating engine class.
include('smarty/Smarty.class.php');

/**
 * Checks whether it is a RSS request
 * the politics map.
 * @return {Boolean} True when we should load the blog.
 */
function isRssRequest() {
  // the rss sections are starting from 50 to 100
  return ($_GET['cid'] && $_GET['cid'] >= 50 && $_GET['cid'] <= 100 );
}

/**
 * Checks whether we should load the blog instead of loading the main page of
 * the politics map.
 * @return {Boolean} True when we should load the blog.
 */
function shouldLoadBlog() {
  return ($_GET['cid'] && $_GET['cid'] == 16) ||  // the blog section
         (!$_GET['cid'] && $_GET['p']) ||         // a specific post
         (!$_GET['cid'] && $_GET['feed']);        // the rss feed
}

if(!isRssRequest()){
	if (shouldLoadBlog()) {
	  /**
	   * Tells WordPress to load the WordPress theme and output it.
	   * TODO(vivi): I don't remember why this needs to be defined here.
	   * @var bool
	   */
	  define('WP_USE_THEMES', true);
	
	  // Loads the WordPress Environment and Template.
	  require('./wp-blog-header.php');
	} else {
	  // If we're not loading the blog, just load our main page. Do load the
	  // wp-config so that we can use the fact that the user is logged in.
	  require_once('./wp-config.php');
	
	  // Loads the Politics Map front page that displays numbers and stuff.
	  require('./hp-index.php');
	}
} else {
	// If we're not loading the blog, just load our main page. Do load the
	// wp-config so that we can use the fact that the user is logged in.
	require_once('./wp-config.php');

	// If we're not loading the blog, just load our main page. Do load the
	// wp-config so that we can use the fact that the user is logged in.
	require('pages/rss/rss_person_declarations.php');
}
?>
