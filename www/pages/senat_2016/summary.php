<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');
include_once('hp-includes/electoral_colleges.php');
include_once('hp-includes/elections_2016.php');

$t = new Smarty();
$colleges = getElectoralCollegeHashByCounty('senat');
foreach ($colleges as $key => $col) {
  $col['seats'] = $seats[ucwords($key)];
  $colleges[$key] = $col;
}

$t->assign("colleges", $colleges);

$t->display('cdep_2016_summary.tpl');
?>
