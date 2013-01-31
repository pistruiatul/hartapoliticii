<?php

$t = new Smarty();
if ($uid == 0) $t->caching = 1;

if (!$t->is_cached('mod_news_main_compact.tpl', $person->id)) {
  $t->assign('id', $person->id);
  $t->assign('title', $title);
  $t->assign('name', str_replace(' ', '+', $person->name));
  $t->assign('news', $person->getMostRecentNewsItems(3));
}
$t->display('mod_news_main_compact.tpl', $person->id);

?>
