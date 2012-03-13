<?php
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT t, url " .
  "FROM catavencu_2008 " .
  "WHERE idperson = $person->id ";

$s = mysql_query($sql);
$r = mysql_fetch_array($s);

$t = new Smarty();
$t->assign('text', $r['t']);
$t->display('mod_catavencu_2008.tpl');
?>
