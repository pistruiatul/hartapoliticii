<?php
// Various functions with functionality related to users of the site, grabbing
// their handle name, permissions, etc.


/**
 * Returns the URL used to log out a logged in user.
 *
 * @param {string} $redirect Optional, the url to redirect the user to after
 *     logging out.
 * @return {string} The url.
 */
function getLogoutUrl($redirect = '') {
	if (strlen($redirect)) {
    $redirect = "&redirect_to={$redirect}";
	}

  return wp_nonce_url(
      "http://www.hartapoliticii.ro/wp-login.php?action=logout{$redirect}",
      'log-out');
}


/**
 * Returns the username of the user with the ID passed in as a parameter.
 *
 * @param {Number} $uid The id of the user we are interested in.
 * @return {String} The user's login handle as a string.
 */
function getUserLogin($uid) {
  $s = mysql_query("SELECT user_login FROM wp_users WHERE ID={$uid}");
  if ($r = mysql_fetch_array($s)) {
    return $r['user_login'];
  }
  return '';
}


/**
 * Returns the user level of the user with the ID passed in as a parameter. The
 * default user level for plain users is 0, and the number increases as the
 * user gets different roles. The user's level can be set by the administrator
 * from the WP admin interface.
 *
 * @param {Number} $uid The id of the user we are interested in.
 * @return {Number} The level of the user.
 */
function getUserLevel($uid) {
  $s = mysql_query(
    "SELECT meta_value
     FROM wp_usermeta
     WHERE user_id = {$uid} AND
           meta_key = 'wp_user_level'");

  if ($r = mysql_fetch_array($s)) {
    return (int)$r['meta_value'];
  }
  return 0;
}


?>
