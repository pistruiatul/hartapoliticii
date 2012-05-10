<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');


/**
 * Returns a list with the most recently added people and the users that added
 * these new people.
 * @return {Array} The array of recently added people.
 */
function getMostRecentNewPeople() {
  $s = mysql_query("
      SELECT idperson, value, display_name, name, time
      FROM moderation_queue
      LEFT JOIN people ON people.id = moderation_queue.idperson
      WHERE type='add_person'
      ORDER BY time DESC
      LIMIT 8");

  $results = array();
  while ($r = mysql_fetch_array($s)) {
    array_push($results, $r);
  }
  return $results;
}


$title = 'Profil';
include('header.php');

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;
$user_login = is_user_logged_in() ? $current_user->user_login : '';

// Grab the tags that this user has created for the Senate and for Cdep.
$senatTags = getTagsList('senat_2008_votes_details', $uid);
$cdepTags = getTagsList('cdep_2008_votes_details', $uid);

// Try to display everything.
$t = new Smarty();
$t->assign('senatTags', $senatTags);
$t->assign('cdepTags', $cdepTags);
$t->assign('user_login', $user_login);

$t->assign('user_is_admin', getUserLevel($uid) > 0);
if (getUserLevel($uid) > 0) {
  // Also show the history of the most recent 5 people added.
  $t->assign('recent_people', getMostRecentNewPeople());
}

$t->display('my_account_summary.tpl');

?>
