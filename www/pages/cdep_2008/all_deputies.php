<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');

$t = new Smarty();
$sortBy = $_GET['sort'] ? (int)$_GET['sort'] : 3;
$t->assign('mostPresent', getCdepSorted($sortBy, "DESC", 400));
$t->display('cdep_2008_all_deputies.tpl');
?>
