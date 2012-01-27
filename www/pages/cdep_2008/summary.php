<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');

$t = new Smarty();

// Show the top most present and absent people.
$t->assign('mostPresent', getCdepSorted(3, "DESC", 10));
$t->assign('leastPresent', getCdepSorted(3, "ASC", 10));

// Show the guys that show up most in the news.
$list = getMostPresentInNews(10, 'cdep/2008');
$list = decorateListWithExtraInfo($list, '2008', 'cdep', 'votes_agg',
                                  'percent');
$list = newsAddPreviousWeekToList($list, 'cdep/2008');
$t->assign('newsPeople', $list);

$t->assign('news', getMostRecentNewsArticles('cdep', '2008', 6));
$t->assign('MOST_RECENT_NEWS_ABOUT', 'Cele mai recente stiri cu deputați');

// Show the latest global votes in the parliament.
$t->assign('mostRecentVotes', getCdepVotes("DESC", 6, 0));

// Make sure the hook for the 'more bla bla link' is okay.
// TODO(vivi): Maybe generalize this so the boxes above benefit.
$t->assign('cid', $cid);
$t->assign('sidVotes', getSidFor('all_votes.php'));

$t->display('cdep_2008_summary.tpl');
?>
