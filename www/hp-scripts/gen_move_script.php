<?php
include('../_top.php');

$s = mysql_query("SELECT id, idperson FROM euro_2009_candidates");

while($r = mysql_fetch_array($s)) {
  echo "cp {$r['id']}.pdf new/{$r['idperson']}_euro_2009_1.pdf\n";
}

include('../_bottom.php');
?>