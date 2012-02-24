<?
include_once('mods/functions_common.php');
include_once('mods/senat_2008_functions.php');

$uid = is_user_logged_in() ? $current_user->ID : 0;

mod_senat_2008_summary();

$t = new Smarty();
$t->assign('cid', $cid);
$t->assign('idperson', $person->id);
$t->assign('name', $person->getUrlName());
$t->assign('votes', mod_senat_2008_get_votes($person, 3, 0));
$t->display('mod_senat_2008_most_recent_votes.tpl');

showBeliefs('senat', '2008', $uid, $person->id);

?>
