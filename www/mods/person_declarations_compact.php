<?php

$declarations = $person->searchDeclarations('', 0, 5, false, 'all');

if (count($declarations) > 0) {
  $t = new Smarty();
  $t->caching = 1;

  if (!$t->is_cached('mod_person_declarations_compact.tpl', $person->id)) {
    $t->assign('id', $person->id);
    $t->assign('name', $person->getNameForUrl());
    $t->assign('person', $person);
    $t->assign('declarations', $declarations);
  }
  $t->display('mod_person_declarations_compact.tpl', $person->id);
}

?>
