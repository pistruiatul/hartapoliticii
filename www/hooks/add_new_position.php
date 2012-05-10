<?php
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) die("You're not logged in");
if (getUserLevel($uid) == 0) die("Not enough privileges");


// Sanitize the inputs a little bit.
$person_id = mysql_real_escape_string($_GET['person_id']);
$title = mysql_real_escape_string($_GET['title']);
$url = mysql_real_escape_string($_GET['url']);
$start_time = mysql_real_escape_string($_GET['start_time']);
$what = mysql_real_escape_string($_GET['what']);

// Also record this in the moderation queue so we can see who added what.
$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);
mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('add_position', $person->id, '{$title} by {$userLogin}', '$ip', ". time() . ")");

// For now, only support gov/ro stuff
if ($what == "gov/ro") {
  // Insert into the people_history the fact that they are part of the gov.
  mysql_query("
        INSERT IGNORE INTO people_history(idperson, what, url, time)
        VALUES({$person_id}, '{$what}', '{$url}', {$start_time})");
  mysql_query("
        DELETE FROM govro_people
        WHERE idperson = {$person_id} AND mintime ={$start_time}");
  mysql_query("
        INSERT IGNORE INTO govro_people(idperson, title, mintime, maxtime, link)
        VALUES({$person_id}, '{$title}', {$start_time}, 0, '{$url}')");
}


echo "Poziția a fost adăugată. ".
     "Vizitează-le pagina <a href=/?cid=9&id={$person_id} ".
     "taget=_blank>aici</a>.";

require_once('../_bottom.php');
?>
