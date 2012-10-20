<?php
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) {
  die("({error:'Trebuie să te autentifici pentru a urmări un politician.'})");
}

// Sanitize the inputs a little bit.
$person_id = mysql_real_escape_string($_GET['person_id']);
$action = mysql_real_escape_string($_GET['action']);

$person = new Person();
$person->setId($person_id);

// Also record this in the moderation queue so we can see who added what.
$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);
mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('follow', $person_id, 'by {$userLogin}', '$ip', ". time() . ")");

if ($action == 'follow') {
  $person->addFollowByUser($uid);
  echo "({new_action:'unfollow'})";
} else {
  $person->removeFollowByUser($uid);
  echo "({new_action:'follow'})";
}

require_once('../_bottom.php');
?>
