<?php
include_once('pages/functions_common.php');
include_once('pages/pres_2009/functions.php');
$t = new Smarty();

// Show the guys that show up most in the news.
$t->assign('candidati', getPresidentialCandidates('pres', '2009', 100));

// Show the guys that show up most in the news.
$list = getMostPresentInNews(10, 'pres/2009');
$list = newsAddPreviousWeekToList($list, 'pres/2009');
$t->assign('newsPeople', $list);

$t->assign('news', getMostRecentNewsArticles('pres', '2009', 10));
$t->assign('MOST_RECENT_NEWS_ABOUT', 'Cele mai recente stiri cu candidați');

$t->assign('video_columns', 7);
$t->assign('videos', getMostRecentVideos(14));

$t->display('pres_2009_summary.tpl');
?>
