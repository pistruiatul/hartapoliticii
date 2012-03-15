<?php

$title = "Declarații";

$pageSize = 10;
$start = (int)$_GET['start'];
$text_mode = $_GET['text_mode'] ? $_GET['text_mode'] : 'snippets';


$dq = mysql_real_escape_string($_GET['dq'] ? $_GET['dq'] : '');

// NOTE(vivi): This code supports snippets too, but for now we are always going
// to display full text. We do that so that the marking of important passages
// gets a little easier to implement.
$declarations = $person->searchDeclarations($dq, $start, $pageSize, true);

$t = new Smarty();

$t->assign('id', $person->id);
$t->assign('name', $person->name);
$t->assign('declarations', $declarations);
$t->assign('dq', $dq);

$t->assign('start', $start);

$t->assign('last_page', sizeof($declarations) < $pageSize);
$t->assign('first_page', $start == 0);

$t->assign('text_mode', $text_mode);

$baseUrl = "/?name={$person->name}&exp=person_declarations&dq={$dq}";

$prevStart = $start - $pageSize;
$t->assign('prev_page_link', $baseUrl . "&start={$prevStart}");
$nextStart = $start + $pageSize;
$t->assign('next_page_link', $baseUrl . "&start={$nextStart}");

$t->assign('full_text_link', $baseUrl . "&start={$start}");
$t->assign('snippets_link', $baseUrl . "&start={$start}");

$t->display('mod_person_declarations_expanded.tpl');

?>
