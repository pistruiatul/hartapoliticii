<?php

include ('../_top.php');

$id = $_GET['id'];
$attribute = $_GET['attribute'];
$value = $_GET['value'];

if ($attribute == "" || $value == "") {
  return;
}

$now = time() * 1000;

mysql_query("
  INSERT IGNORE INTO people_facts(idperson, attribute, value, time_ms)
  VALUES({$id}, '{$attribute}', '{$value}', {$now})
");

include ('../_bottom.php');

?>
