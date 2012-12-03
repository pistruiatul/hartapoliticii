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

if ($uid != 1) {
  die("");
}

$latlng = explode(",", $_GET['latlng']);
$lat = trim($latlng[0]);
$lng = trim($latlng[1]);

$markerCodes = explode(",", $_GET['markerCode']);

foreach ($markerCodes as $markerCode) {
  // nr_sv-nr_cir
  $parts = explode("-", $markerCode);

  // Add the vote in the votes table.
  mysql_query("
    UPDATE sectii_vot SET lat={$lat}, lon={$lng}
    WHERE nr_sv={$parts[0]} AND nr_cir={$parts[1]}
  ");


}

echo "Updated to [{$lat},{$lng}]";

require_once('../_bottom.php');
?>
