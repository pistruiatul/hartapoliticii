<?php

$title = "Declarații";

$pageSize = 10;
$start = (int)$_GET['start'];
$text_mode = $_GET['text_mode'] ? $_GET['text_mode'] : 'snippets';


$dq = mysql_real_escape_string($_GET['dq'] ? $_GET['dq'] : '');
$declarations = $person->searchDeclarations($dq, $start, $pageSize,
                                            $text_mode == 'full_text');

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

$t->assign('full_text_link', $baseUrl . "&start={$start}&text_mode=full_text");
$t->assign('snippets_link', $baseUrl . "&start={$start}&text_mode=snippets");

$t->display('mod_person_declarations_expanded.tpl');

?>
