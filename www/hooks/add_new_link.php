<?php
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');


/**
 * @param $link {String} Already mysql-escaped link.
 * @return Boolean
 */
function addLinkToNewsQueue($link, $uid, $userDisplayName, $origin) {
  $s = mysql_query("
    SELECT * FROM news_queue WHERE link = '{$link}'
  ");
  if (mysql_num_rows($s) > 0) {
    return false;
  }

  mysql_query("
    INSERT INTO news_queue(user_id, user_name, link, origin, time_ms)
    values($uid, '{$userDisplayName}', '{$link}', '{$origin}', " . time() . ")
  ");

  return true;
}


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) {
  die("Doar utilizatorii autentificați pot propune link-uri. " .
      "<a href='/wp-login.php?action=login'>Autentifică-te</a>.");
}

// Also record this in the moderation queue so we can see who added what.
$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);
$userDisplayName = getUserDisplayName($uid);


// Sanitize the inputs a little bit.
$link = mysql_real_escape_string($_GET['link']);

// Sanitize the inputs a little bit.
$origin = mysql_real_escape_string($_GET['origin']);

// Log this to my moderation queue for the administrator to see what is
// happening.
mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('add_link', 0, '{$link} by {$userLogin} for {$origin}', '$ip', ". time() . ")");


if (addLinkToNewsQueue($link, $uid, $userDisplayName, $origin)) {
  echo "Link-ul este acum în coada de moderare. ".
       "Aprobarea durează <b>maxim 5 minute</b>.".
       "<br>Poți verifica statusul " .
       "resurselor trimise de tine pe " .
       "<a href='/?cid=profile'>pagina ta de profil</a>";
} else {
  echo "Altcineva a adăugat deja acest link!";
}

require_once('../_bottom.php');
?>
