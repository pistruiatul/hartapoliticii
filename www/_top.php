<?php
  include_once('secret/db_user.php');

  $dblink = mysql_connect($DB_HOST, $DB_USER, $DB_PASS) or die("Could not connect");

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
