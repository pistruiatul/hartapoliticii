<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');

$t = new Smarty();

// Show the guys that show up most in the news.
$list = getMostPresentInNews(10, 'alegeri/cdep/2012');
$list = newsAddPreviousWeekToList($list, 'alegeri/cdep/2012');
$t->assign('newsPeople', $list);

$t->assign('news', getMostRecentNewsArticles('alegeri/cdep', '2012', 6));
$t->assign('MOST_RECENT_NEWS_ABOUT', 'Cele mai recente stiri cu candidați');

$t->display('cdep_2012_summary.tpl');
?>
