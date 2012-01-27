<?php
include_once('pages/functions_common.php');
include_once('pages/senat_2008/functions.php');
include_once('mods_parties/functions_common.php');

$t = new Smarty();

$t->assign('party_short_name', $party->name);

$all_votes = getAllFinalVotes('senat', '2008',  $party->id);
$party_votes = getPartyFinalVotes('senat', '2008', $party->id);
$party_line_votes = getPartyLineFinalVotes('senat', '2008', $party->id);

$t->assign('not_voted_votes', $all_votes - $party_votes);
$t->assign('non_party_line_votes', $party_votes - $party_line_votes);
$t->assign('party_line_votes', $party_line_votes);

$t->assign('not_voted_votes_percent',
    100 * ($all_votes - $party_votes) / $all_votes);
$t->assign('non_party_line_votes_percent',
    100 * ($party_votes - $party_line_votes) / $all_votes);
$t->assign('party_line_votes_percent', 100 * $party_line_votes / $all_votes);

// Show the top most present and absent people.
$t->assign('presTop', getSenatSorted(3, "DESC", 3, $party->id));
$t->assign('presBot', array_reverse(getSenatSorted(3, "ASC", 3, $party->id)));

// Show the top mavericks.
$t->assign('maverickTop', getSenatSorted(4, "ASC", 3, $party->id));
$t->assign('maverickBot',
    array_reverse(getSenatSorted(4, "DESC", 3, $party->id)));

$t->assign('see_all_cid', 12);
$t->display('party_mod_cdep2008_compact.tpl');

?>
