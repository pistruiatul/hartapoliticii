<?php
include_once('mods/functions_common.php');
include_once('mods/senat_2008_functions.php');

$uid = is_user_logged_in() ? $current_user->ID : 0;

mod_senat_2008_summary();

$start = $_GET['start'] ? (int)$_GET['start'] : 0;
$maverick = $_GET['maverick'] ? (int)$_GET['maverick'] : -1;

$t = new Smarty();
$t->assign('cid', $cid);
$t->assign('idperson', $person->id);
$votes = mod_senat_2008_get_votes($person, 50, $start, $maverick);
$t->assign('votes', $votes);

$t->assign('nextStart', sizeof($votes) == 50 ? $start + 50 : -1);
$t->assign('prevStart', $start > 0 ? $start - 50 : -1);
$t->assign('start', $start + 1);
$t->assign('maverick', $maverick);

$t->display('mod_senat_2008_all_votes.tpl');

showBeliefs('senat', '2008', $uid, $person->id);

?>
