<?php
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT * " .
  "FROM people_facts " .
  "WHERE idperson = {$person->id} AND attribute='romaniacurata/2012'";
$s = mysql_query($sql);

$content = array();
while ($r = mysql_fetch_array($s)) {
  $content[] = $r['value'];
}

$sql =
  "SELECT * " .
  "FROM people_history " .
  "WHERE idperson = $person->id AND what='romaniacurata/2012'";
$shistory = mysql_query($sql);
$rhistory = mysql_fetch_array($shistory);


$t = new Smarty();

$text = preg_replace("/(\n+)/", "<div style='height:12px;'></div>",
                     implode("\n", $content));
$t->assign('text', $text);

$t->assign('source', $rhistory['url']);
$t->display('mod_romaniacurata_2011.tpl');
?>
