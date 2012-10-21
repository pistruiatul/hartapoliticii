<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}

include('hp-includes/people_lib.php');

function getCountUsersFollowingOthers() {
  $s = mysql_query("SELECT distinct(user_id) FROM wp_usermeta WHERE meta_key='follow'");
  return mysql_num_rows($s);
}

function getCountUsers() {
  $s = mysql_query("SELECT distinct(ID) FROM wp_users");
  return mysql_num_rows($s);
}

function getCountPeopleBeingFollowed() {
  $s = mysql_query("SELECT meta_value FROM wp_usermeta WHERE meta_key='follow'");
  return mysql_num_rows($s);
}


function getRecentSearches() {
  $searches = array();
  $s = mysql_query(
    "SELECT * FROM log_searches ORDER BY time DESC LIMIT 0, 15");
  while ($r = mysql_fetch_array($s)) {
    $searches[] = $r;
  }
  return $searches;
}


$title = "Despre acest site / Contact";

include('header.php');

$t = new Smarty();

$t->assign('follow_users', getCountUsersFollowingOthers());
$t->assign('following_people', getCountPeopleBeingFollowed());
$t->assign('total_people', sizeof($people));
$t->assign('count_users', getCountUsers());

$t->assign('recent_searches', getRecentSearches());

$t->display('about.tpl');

?>
