<?php

$t = new Smarty();

$t->caching = 1;
if ($uid > 0 || !$t->is_cached('person_most_recent_news.tpl',
                               $uid . "-" . $person->id)) {
  $t->assign('id', $person->id);
  $t->assign('title', $title);
  $t->assign('name', str_replace(' ', '+', $person->name));
  $t->assign('news', $person->getMostRecentNewsItems(7));
}
$t->display('person_most_recent_news.tpl', $uid . "-" . $person->id);

?>
