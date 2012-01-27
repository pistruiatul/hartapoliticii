<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');

$t = new Smarty();

$PAGE_SIZE = 50;
$from = (int)$_GET['from'];

$t->assign('votes', getCdepVotes("DESC", 50, $from));
$t->assign('cid', $cid);
$t->assign('sid', $sid);

$t->assign('from', $from);
$t->assign('fromPrev', max(0, $from - $PAGE_SIZE));
$t->assign('fromNext', $from + $PAGE_SIZE);

$t->display('cdep_2008_all_votes.tpl');
?>
