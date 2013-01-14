<?php
include_once('pages/functions_common.php');
include_once('pages/cdep_2008/functions.php');
include_once('pages/senat_2008/functions.php');

include_once('hp-includes/follow_graph.php');
include_once('hp-includes/declarations.php');

include_once('hp-includes/news.php');

$t = new Smarty();
$t->caching = 1;
if ($uid > 0 || !$t->is_cached('home_page_summary.tpl')) {
  // Show the guys that show up most in the news.
  $list = getMostPresentInNews(10, NULL, NULL, NULL, NULL, NULL);
  $list = newsAddPreviousWeekToList($list, NULL, '%');
  $t->assign('topPeople', $list);

  if ($uid > 0) {
    // Show the guys that show up most in the news.
    $followList = getMostPresentInNews(10, NULL, NULL, NULL,
                                       followedPeopleIdsAsArray(), NULL);
    $followList = newsAddPreviousWeekToList($followList, NULL, '%');
  } else {
    $followList = array();
  }
  $t->assign('followedPeople', $followList);

  $t->assign('news', getMostRecentNewsArticles(NULL, NULL, 7, '%'));
  $t->assign('blogposts', getMostRecentBlogPosts(7));

  $t->assign("links", getMostRecentUgcLinks(3, NULL, 0, time() - 3 * 86400));

  // Get the top three senators.
  $t->assign('top_senators', getSenatSorted(3, 'DESC', 3));
  $t->assign('bottom_senators',array_reverse(getSenatSorted(3, 'ASC', 3)));

  // Get the top three senators.
  $t->assign('top_cdep', getCdepSorted(3, 'DESC', 3));
  $t->assign('bottom_cdep', array_reverse(getCdepSorted(3, 'ASC', 3)));

  $parties = array(
    array("id" => "1", "logo" => "images/parties/1.gif"),
    array("id" => "2", "logo" => "images/parties/2.png"),
    array("id" => "7", "logo" => "images/parties/7.jpg"),
    array("id" => "14", "logo" => "images/parties/14.jpg"),
  );
  $t->assign('parties', $parties);

  $t->assign('declarations', getMostRecentDeclarations());
}
$t->display('home_page_summary.tpl', $uid);

?>
