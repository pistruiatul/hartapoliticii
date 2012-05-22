<?php
  include_once('secret/db_user.php');

  if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'zen.dev') {
    $dblink = mysql_connect("localhost", $DB_USER, $DB_PASS) or die("Could not connect");
  } else {
    $dblink = mysql_connect("localhost:/tmp/mysql5.sock", $DB_USER, $DB_PASS) or die("Could not connect");
  }

  mysql_set_charset('UTF8', $dblink);

  mysql_select_db($DB_NAME, $dblink) or die("Could not select database");

  function info($msg) {
    $msg = str_replace("[", "[<font color=red>", $msg);
    $msg = str_replace("]", "</font>]", $msg);
    $msg = str_replace("(", "(<font color=green>", $msg);
    $msg = str_replace(")", "</font>)", $msg);
    $msg = str_replace("{", "{<font color=purple>", $msg);
    $msg = str_replace("}", "</font>}", $msg);

    echo date('h:i:s') . ': <font color=#999999>' . $msg . '</font><br>';
  }
?>
