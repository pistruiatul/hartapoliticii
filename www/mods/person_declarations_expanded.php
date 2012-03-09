<?php

$title = "Declarații";

$pageSize = 10;
$start = (int)$_GET['start'];

$dq = mysql_real_escape_string($_GET['dq'] ? $_GET['dq'] : '');
$declarations = $person->searchDeclarations($dq, $start, $pageSize);

$t = new Smarty();

$t->assign('id', $person->id);
$t->assign('name', $person->name);
$t->assign('declarations', $declarations);
$t->assign('dq', $dq);

$t->assign('start', $start);

$t->assign('last_page', sizeof($declarations) < $pageSize);
$t->assign('first_page', $start == 0);

$baseUrl = "/?name={$person->name}&exp=person_declarations&dq={$dq}";

$prevStart = $start - $pageSize;
$t->assign('prev_page_link', $baseUrl . "&start={$prevStart}");
$nextStart = $start + $pageSize;
$t->assign('next_page_link', $baseUrl . "&start={$nextStart}");

$t->display('mod_person_declarations_expanded.tpl');

?>
