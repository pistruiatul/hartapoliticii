<?php

$t = new Smarty();
$t->caching = 1;

if (!$t->is_cached('mod_news_expanded.tpl', $person->id)) {
  // The list of news is here.
  $start = (int)$_GET['start'];

  $t->assign('next', $start + 10);
  $t->assign('prev', $start - 10);

  $t->assign('id', $person->id);
  $t->assign('name', $person->getUrlName());
  $t->assign('news', $person->getMostRecentNewsItems(10, $start));

  // Top associations are supposed to go here.
  $associates = $person->getTopNewsAssociates(7);
  $t->assign('assoc', $associates);
  $t->assign('total_news', $associates[0]['cnt']);
}
$t->display('mod_news_expanded.tpl', $person->id);

?>
