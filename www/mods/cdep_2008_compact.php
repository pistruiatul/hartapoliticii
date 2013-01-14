<?php
include_once('mods/functions_common.php');
include_once('mods/cdep_2008_functions.php');

$uid = is_user_logged_in() ? $current_user->ID : 0;

mod_cdep_2008_summary();

$t = new Smarty();

$t->caching = 1;

if (!$t->is_cached('mod_cdep_2008_most_recent_votes.tpl', $person->id)) {
  $t->assign('cid', $cid);
  $t->assign('idperson', $person->id);
  $t->assign('name', $person->getUrlName());
  $t->assign('votes', mod_cdep_2008_get_votes($person, 5));
}
$t->display('mod_cdep_2008_most_recent_votes.tpl', $person->id);

showBeliefs('cdep', '2008', $uid, $person->id);

?>
