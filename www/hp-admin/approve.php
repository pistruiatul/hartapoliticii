<?php 
include_once('../_top.php');

$table = mysql_real_escape_string($_GET['table']);

$approve = (int)$_GET['approve'];
$id = (int)$_GET['id'];

mysql_query("
  UPDATE `$table` SET approved=$approve WHERE id=$id
  ");

include_once('../_bottom.php');
?>
