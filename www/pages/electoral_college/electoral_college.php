<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/electoral_colleges.php');
include_once('hp-includes/news.php');

$college_name = mysql_real_escape_string(ucwords($_GET['colegiul']));

$title = "Colegiul uninominal " . $college_name;
include('header.php');

$t = new Smarty();

$t->assign("college_name", $college_name);

$t->assign("pc_county_short", getCollegeCountyShort($college_name));
$t->assign("pc_number", getCollegeNumber($college_name));
$t->assign("pc_id", startsWith($college_name, "D") ? 15 : 14);

$t->assign("descriptions", getDescriptionsForCollege($college_name));
$t->assign("description_source", getDescriptionSourceForCollege($college_name));

$t->assign("candidates_2008", getResults2008ForCollege($college_name));
$t->assign("id_winner_2008", getWinner2008ForCollege($college_name));
$t->assign("show_minorities_link", strpos($college_name, "D") === 0);

$t->assign("compact", false);
$t->assign("candidates_2012", getCollegeCandidates($college_name, "2012"));
$t->assign("news", getMostRecentNewsArticles(
                       NULL, NULL, 5, '%',
                       getCollegePeopleIds($college_name, "2012")));

$t->assign("links", array());

if (endsWith(strtolower($college_name), "strainatate")) {
  $t->assign("college_image", "/images/{$college_name}.jpg");
}

$t->display("electoral_college.tpl");

?>