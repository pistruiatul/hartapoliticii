<?php

include_once('hp-includes/electoral_colleges.php');

// We know that the person we are talking about is $person.
$sql = "SELECT colegiu FROM results_2012 WHERE idperson = {$person->id}";
$r = mysql_fetch_array(mysql_query($sql));
$college = $r['colegiu'];

$t = new Smarty();
$t->assign("college_name", $college);

$t->assign("compact", true);
$t->assign("candidates", getCollegeCandidates($college, "2012"));

$t->display("mod_results_2012.tpl");


?>
