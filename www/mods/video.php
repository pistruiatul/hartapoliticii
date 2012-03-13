<?php
$t = new Smarty();
$t->assign('video_columns', 5);
$t->assign('videos', $person->getMostRecentVideos(5));
$t->display('video_section.tpl');
?>
