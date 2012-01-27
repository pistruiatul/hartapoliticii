<?php
include ('../_top.php');
include ('../functions.php');

$time = time();
$name = trim($_GET['name']);
$qualifier = urldecode(trim($_GET['qualifier']));
$state = trim($_GET['state']);

echo $qualifier;

$sql = "
  UPDATE news_qualifiers SET approved = $state
  WHERE name = '$name' AND qualifier = '$qualifier'
";

mysql_query($sql);

include ('../_bottom.php');
?>