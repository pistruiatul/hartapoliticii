<?
include ('_top.php');

// We should have type, pid, and url which means what to take and from where.
$type = mysql_real_escape_string($_GET['type']);
$pid = mysql_real_escape_string($_GET['pid']);
$value = mysql_real_escape_string($_GET['value']);

$ip = $_SERVER['REMOTE_ADDR'];

mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('$type', $pid, '$value', '$ip', ". time() . ")");

echo 'hi';

include ('_bottom.php');
?>