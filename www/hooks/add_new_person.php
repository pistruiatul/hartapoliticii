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
$name = mysql_real_escape_string($_GET['name_all']);
$displayName = mysql_real_escape_string($_GET['display_name']);
$photoUrl = mysql_real_escape_string($_GET['photo_url']);

$person = new Person();
$person->setName($name);

$person->addExtraNames($displayName);
$person->setDisplayName($displayName);

$person->addToDatabaseIfNobody();

// Now also set the image URL.
if ($photoUrl != '') {
  downloadPersonPhoto($person->id, $photoUrl);
}

// Also record this in the moderation queue so we can see who added what.
$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);
mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('add_person', $person->id, 'by {$userLogin}', '$ip', ". time() . ")");


echo "Persoana X a fost adăugată. ".
     "Vizitează-le pagina <a href=/?cid=9&id={$person->id} ".
     "taget=_blank>aici</a>.";

require_once('../_bottom.php');
?>
