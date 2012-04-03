<?php
include_once('pages/functions_common.php');
include_once('pages/senat_2008/functions.php');

$t = new Smarty();
$t->assign('cid', $cid);
$sortBy = $_GET['sort'] ? (int)$_GET['sort'] : 3;
$t->assign('mostPresent', getSenatSorted($sortBy, "DESC", 400));
$t->display('senat_2008_all_senators.tpl');
?>
