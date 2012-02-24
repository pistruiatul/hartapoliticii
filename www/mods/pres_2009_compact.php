<?php

$t = new Smarty();

$show_sql = "
  SELECT p.details, parties.name AS partyname
  FROM pres_2009_people AS p
  LEFT JOIN parties ON p.idparty = parties.id
  WHERE idperson={$person->id}
";

$r = mysql_fetch_array(mysql_query($show_sql));
$t->assign('details', stripslashes($r['details']));
$t->assign('party', $r['partyname']);

$content_id = getContentUpdateId(
    "UPDATE pres_2009_people SET details='{details}' ".
    "WHERE idperson={$person->id}");

if ($_SERVER['SERVER_NAME'] == 'localhost') {
  $t->assign('content_id', $content_id);
  $t->assign('displayEdit', true);
}

$t->assign('video_columns', 5);
$t->assign('videos', $person->getMostRecentVideos(5));

$t->display('mod_pres_2009_details.tpl');

?>
