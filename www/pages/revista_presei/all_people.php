<?php
include_once('pages/functions_common.php');

$t = new Smarty();

// Show the guys that show up most in the news.
$list = getMostPresentInNews(200);
$list = newsAddPreviousWeekToList($list);
$list = newsAddMostRecentArticle($list);

$t->assign('numArticles', countAllMostRecentNews(7));
$t->assign('topPeople', $list);
$t->assign('SHOW_LATEST_ARTICLE', true);

$t->display('revista_presei_all_people.tpl');
?>
