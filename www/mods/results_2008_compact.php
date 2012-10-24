<?php

include_once('hp-includes/electoral_colleges.php');

// We know that the person we are talking about is $person.
$sql = "SELECT colegiu FROM results_2008 WHERE idperson = {$person->id}";
$r = mysql_fetch_array(mysql_query($sql));
$college = $r['colegiu'];

$t = new Smarty();
$t->assign("college_name", $college);
$t->assign("candidates", getResults2008ForCollege($college));
$t->assign("id_winner", getWinner2008ForCollege($college));
$t->display("mod_results_2008.tpl");


?>
