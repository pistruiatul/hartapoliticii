<?php

include_once('hp-includes/electoral_colleges.php');


$t = new Smarty();
$t->assign("college_name", $college);

$t->assign("compact", true);
$t->assign("candidates", getCollegeCandidates($college, "2012"));

$t->display("mod_results_2012.tpl");


?>
