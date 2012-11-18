<?php
include ('../_top.php');

include_once('../hp-includes/person_class.php');
include_once('../hp-includes/people_util.php');

include_once('../mods/functions_common.php');
include_once('../pages/functions_common.php');

include_once('../smarty/Smarty.class.php');

function getUnmoderatedNewsQueue() {
  $s = mysql_query("
    SELECT * FROM news_queue WHERE status = 0
  ");
  $results = array();
  while ($r = mysql_fetch_array($s)) {
    $results[] = $r;
  }
  return $results;
}


$t = new Smarty();
$t->assign('links', getUnmoderatedNewsQueue());
$t->display('api_unmoderated_news_queue.tpl');

?>
