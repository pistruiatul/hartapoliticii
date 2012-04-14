<?php

$declarations = $person->searchDeclarations('', 0, 5, false, 'all');

if (sizeof($declarations) > 0) {
  $t = new Smarty();

  $t->assign('id', $person->id);
  $t->assign('name', str_replace(' ', '+', $person->name));
  $t->assign('declarations', $declarations);

  $t->display('mod_person_declarations_compact.tpl');
}
?>
