<?php

$t = new Smarty();
$t->caching = 2;
$t->cache_lifetime = 86400;

if (!$t->is_cached('mod_person_declarations_compact.tpl', "{$person->id}")) {
  $declarations = $person->searchDeclarations('', 0, 5, false, 'all');

  $t->assign('person', $person);
  $t->assign('id', $person->id);
  $t->assign('name', $person->getNameForUrl());
  $t->assign('declarations', $declarations);
}
$t->display('mod_person_declarations_compact.tpl', "{$person->id}");

?>
