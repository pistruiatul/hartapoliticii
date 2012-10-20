<?php

// A library to deal with following of politicians by users. Right now,
// including this library results in the loading as a global hash of the
// list of ids that the logged in user is following. We will use this global
// hash throughout the code to highlight followed politicians where appropriate.

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

$following = array();
if ($uid != 0) {
  $following = getFollowHash($uid);
}


function getFollowHash($uid) {
  $s = mysql_query("
    SELECT user_id, meta_value
    FROM wp_usermeta
    WHERE user_id = {$uid} AND meta_key = 'follow'
  ");


  $hash = array();
  while ($r = mysql_fetch_array($s)) {
    $hash[$r['meta_value']] = $r['meta_value'];
  }

  return $hash;
}




?>