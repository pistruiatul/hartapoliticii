<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

include ('smarty/Smarty.class.php');

if ($_GET['cid'] && $_GET['cid'] == 'suggest') {
  require('./hp-includes/suggest.php');

} else if (($_GET['cid'] && $_GET['cid'] == 16) ||
           (!$_GET['cid'] && $_GET['p']) ||
           (!$_GET['cid'] && $_GET['feed'])) {
  /** Loads the WordPress Environment and Template */
  require('./wp-blog-header.php');

} else {
  require_once('./secret/db_user.php');
  require_once('./wp-config.php');

  /** Loads the Politics Map front page that displays numbers and stuff */
  require('./hp-index.php');
}
?>
