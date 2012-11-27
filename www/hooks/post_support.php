<?php
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');
include_once('../hp-includes/ugc_utils.php');


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) {
  die("Trebuie să fii " .
      "<a href='/wp-login.php?action=login'>autentificat</a>.");
}

$personId = (int)$_GET['person_id'];
$fbUserId = mysql_real_escape_string($_GET['fb_user_id']);
$fbActionId = mysql_real_escape_string($_GET['fb_action_id']);
$delete = (int)$_GET['delete'];

if ($delete == 1) {
  // Add the vote in the votes table.
  mysql_query("
    DELETE FROM people_support
    WHERE user_id = {$uid} AND fb_action_id = {$fbActionId}
  ");
  echo "Deleted!";

} else {
  // Add the vote in the votes table.
  mysql_query("
    INSERT IGNORE INTO people_support(user_id, fb_user_id, person_id, fb_action_id)
    VALUES({$uid}, {$fbUserId}, {$personId}, {$fbActionId})
  ");
  echo "Done!";
}

require_once('../_bottom.php');
?>
