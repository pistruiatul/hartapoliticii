<?php 
include_once('../_top.php');
include_once('../functions.php');
include_once('../hp-includes/people_lib.php');
include_once('../smarty/Smarty.class.php');

$sql = mysql_query("
  SELECT v.id, v.idperson, v.thumb, v.title, v.player_url, v.time, v.duration,
         v.watch_url, p.name
  FROM yt_videos AS v
  LEFT JOIN people AS p ON p.id = v.idperson
  WHERE v.approved = 0
  ORDER BY idperson ASC, time DESC
  ");
$videos = array();
while ($r = mysql_fetch_array($sql)) {
  $videos[] = $r;
}

$t = new Smarty();
$t->assign('videos', $videos);
$t->display('admin_videos.tpl');
?>
