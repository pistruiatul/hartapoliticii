<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');

$title = 'Profil';
include('header.php');

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;
$user_login = is_user_logged_in() ? $current_user->user_login : '';

// Grab the tags that this user has created for the Senate and for Cdep.
$senatTags = getTagsList('senat_2008_votes_details', $uid);
$cdepTags = getTagsList('cdep_2008_votes_details', $uid);

// Try to display everything.
$t = new Smarty();
$t->assign('senatTags', $senatTags);
$t->assign('cdepTags', $cdepTags);
$t->assign('user_login', $user_login);

$t->assign('show_add_person', getUserLevel($uid) > 0);

$t->display('my_account_summary.tpl');

?>
