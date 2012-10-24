<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/electoral_colleges.php');

$college_name = mysql_real_escape_string(ucwords($_GET['colegiul']));

$title = "Colegiul electoral " . $college_name;
include('header.php');

$t = new Smarty();

$t->assign("college_name", $college_name);

$t->assign("candidates_2008", getResults2008ForCollege($college_name));
$t->assign("id_winner_2008", getWinner2008ForCollege($college_name));
$t->assign("show_minorities_link", strpos($college_name, "D") == 0);

$t->display("electoral_college.tpl");

?>