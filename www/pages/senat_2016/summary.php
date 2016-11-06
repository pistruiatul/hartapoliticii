<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');
include_once('hp-includes/electoral_colleges.php');

$t = new Smarty();

$t->assign("colleges", getElectoralCollegeHashByCounty('senat'));

$t->display('cdep_2016_summary.tpl');
?>
