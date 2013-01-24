<?php

$t = new Smarty();
$t->caching = 1;

if ($uid > 0 || !$t->is_cached('mod_news_main_compact.tpl',
                               $uid . "-" . $person->id)) {
  $t->assign('id', $person->id);
  $t->assign('title', $title);
  $t->assign('name', str_replace(' ', '+', $person->name));
  $t->assign('news', $person->getMostRecentNewsItems(3));
}
$t->display('mod_news_main_compact.tpl', $uid . "-" . $person->id);

?>
