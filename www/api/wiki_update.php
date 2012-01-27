<?php
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/wiki_edits.php');

$id = (int)$_GET['id'];

$s = mysql_query("SELECT query FROM wiki_edits WHERE id=$id");
if ($r = mysql_fetch_array($s)) {
  $q = $r['query'];
}

foreach ($_POST as $key => $value) {
  $q = str_replace("{" . $key . "}", mysql_escape_string($value), $q);
}

mysql_query($q);
echo 'ok';

include ('../_bottom.php');
?>
