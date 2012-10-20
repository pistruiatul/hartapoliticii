<?php

include_once('hp-includes/people_lib.php');

// A library to deal with following of politicians by users. Right now,
// including this library results in the loading as a global hash of the
// list of ids that the logged in user is following. We will use this global
// hash throughout the code to highlight followed politicians where appropriate.

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

$followPeopleHashById = array();
if ($uid != 0) {
  $followPeopleHashById = getFollowPeopleHashById($uid);
}


function getFollowPeopleHashById($uid) {
  global $peopleHashById;

  $s = mysql_query("
    SELECT user_id, meta_value
    FROM wp_usermeta
    WHERE user_id = {$uid} AND meta_key = 'follow'
  ");

  $hash = array();
  while ($r = mysql_fetch_array($s)) {
    $hash[$r['meta_value']] = $peopleHashById[(int)$r['meta_value']];
  }
  return $hash;
}


/**
 * Returns the list of people this user follows as an array of values instead
 * of as a hash. This is useful in running queries over mysql for filtering
 * the news for example.
 *
 * @return array
 */
function followedPeopleIdsAsArray() {
  global $uid;
  global $followPeopleHashById;

  if ($uid == 0) return array();

  $result = array();
  foreach($followPeopleHashById as $key => $value) {
    $result[] = $key;
  }

  return $result;
}

/**
 * Returns the list of people this user follows as an array of Person objects.
 *
 * @return array
 */
function followedPeopleAsArray() {
  global $uid;
  global $followPeopleHashById;

  if ($uid == 0) return array();

  $result = array();
  foreach($followPeopleHashById as $key => $person) {
    $result[] = $person;
  }

  return $result;
}


?>