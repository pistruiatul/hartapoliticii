<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');


$title = 'Profil';

include('header.php');

$uid = is_user_logged_in() ? $current_user->ID : 0;
$user_login = is_user_logged_in() ? $current_user->user_login : '';

$senatTags = getTagsList('senat_2008_votes_details', $uid);
$cdepTags = getTagsList('cdep_2008_votes_details', $uid);

$t = new Smarty();
$t->assign('senatTags', $senatTags);
$t->assign('cdepTags', $cdepTags);
$t->assign('user_login', $user_login);
$t->display('my_account_summary.tpl');

?>
