<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/elections_2016.php');
include_once('hp-includes/person_class.php');
include_once('hp-includes/party_class.php');
include_once('hp-includes/news.php');

$county_name = mysql_real_escape_string(ucwords($_GET['colegiul']));
$college_name = mysql_real_escape_string(ucwords($_GET['cam']) . " " . ucwords($_GET['colegiul']));

$title = "Județul " . $college_name;
include('header.php');

$t = new Smarty();

if ($uid == 0) {
  // Enable caching only for logged out users.
  $t->caching = 1;
}

// $college_name here is something like "D Arad" or "S Cluj" - meaning the chamber and the county
// for which we're trying to display the lists.
if (!$t->is_cached("county_college.tpl", "new" . $college_name)) {
  $t->assign("college_name", $college_name);
  $t->assign("county_name", $county_name);

  $cam = explode(" ", $college_name)[0] == "S" ? "Senat" : "Camera Deputaților";
  $t->assign("cam", $cam);

  $parties = getPartiesOnCountyList($college_name);
  $t->assign("parties", $parties);

  $collegePeopleIds = getPeopleIdsInCountyLists($college_name);
  $t->assign("compact", false);
  $t->assign("news", getMostRecentNewsArticles(
                         NULL, NULL, 5, '%', $collegePeopleIds));
  $t->assign("links", getMostRecentUgcLinks(5, $collegePeopleIds));

  if (endsWith(strtolower($college_name), "strainatate")) {
    $t->assign("college_image", "/images/{$college_name}.jpg");
  }
}

$t->display("county_college.tpl", $college_name);

?>