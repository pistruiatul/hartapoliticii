<?php

$t = new Smarty();

$t->assign('id', $person->id);
$t->assign('title', $title);
$t->assign('name', str_replace(' ', '+', $person->name));
$t->assign('news', $person->getMostRecentNewsItems(3));

$t->display('mod_news_main_compact.tpl');

?>
