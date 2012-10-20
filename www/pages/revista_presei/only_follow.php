<?php

$t = new Smarty();

if ($uid == 0) {
  $t->display('revista_presei_follow_explain.tpl');

} else {
  $t->assign('news', getMostRecentNewsArticles(NULL, NULL, 10, '%',
                                               followedPeopleIdsAsArray()));
  $t->display('revista_presei_news_list.tpl');
}
?>
