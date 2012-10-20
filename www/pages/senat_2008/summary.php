<?php
include_once('pages/functions_common.php');
include_once('pages/senat_2008/functions.php');

include_once('hp-includes/news.php');

$t = new Smarty();

// Here do some magical stuff to actually show statistics. :-)
// We don't know how to do that yet. :-S

// For starters, let's select the top present people and top absent people,
// in some SQL queries.

$t->assign('mostPresent', getSenatSorted(3, "DESC", 10));
$t->assign('leastPresent', getSenatSorted(3, "ASC", 10));

$list = getMostPresentInNews(10, 'senat/2008');
$list = decorateListWithExtraInfo($list, '2008', 'senat', 'votes_agg',
                                  'percent');
$list = newsAddPreviousWeekToList($list, 'senat/2008');
$t->assign('newsPeople', $list);
// votes_agg, percent

$t->assign('news', getMostRecentNewsArticles('senat', '2008', 6));
$t->assign('MOST_RECENT_NEWS_ABOUT', 'Cele mai recente știri cu senatori');

$t->assign('mostRecentVotes', getSenatVotes("DESC", 6, 0));

$t->assign('cid', $cid);
$t->assign('sidVotes', getSidFor('all_votes.php'));

$t->display('senat_2008_summary.tpl');
?>
