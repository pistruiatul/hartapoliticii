<?
include ('../_top.php');

include_once('../hp-includes/person_class.php');
include_once('../hp-includes/people_util.php');

include_once('../mods/functions_common.php');
include_once('../pages/functions_common.php');

include_once('../smarty/Smarty.class.php');



// Get the variables out of the URL.
$tagId = (int)$_GET['tagId'];
$room = $_GET['room'] == 'cdep' ? 'cdep' : 'senat';
$personId = (int)$_GET['personId'];
$year = (int)$_GET['year'];


$t = new Smarty();
$votes = getVotesForTag($room, $year, $tagId, $personId);

$t->assign('votes', $votes);
$t->display('api_compass_vote_details.tpl');

?>
