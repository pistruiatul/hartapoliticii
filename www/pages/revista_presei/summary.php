<?php

$t = new Smarty();
$t->assign('news', getMostRecentNewsArticles(NULL, NULL, 10, '%'));
$t->display('revista_presei_news_list.tpl');

?>
