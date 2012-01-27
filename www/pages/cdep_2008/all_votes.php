<?php
include_once('pages/cdep_2008/functions.php');
include_once('pages/functions_common.php');

$t = new Smarty();

$PAGE_SIZE = 50;
$from = (int)$_GET['from'];
$q = (int)$_GET['q'];

$uid = is_user_logged_in() ? $current_user->ID : 0;

$t->assign('votes', getCdepVotes("DESC", 50, $from, $uid, $q));
$t->assign('cid', $cid);
$t->assign('sid', $sid);
$t->assign('is_user_logged_in', is_user_logged_in());

$t->assign('from', $from);
$t->assign('fromPrev', max(0, $from - $PAGE_SIZE));
$t->assign('fromNext', $from + $PAGE_SIZE);

$t->display('cdep_2008_all_votes.tpl');
?>
