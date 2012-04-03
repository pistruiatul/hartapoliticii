<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$FLAG_CAN_CHANGE_DB = true;

// Functions go here.
function fixResults2008Tables() {
  global $FLAG_CAN_CHANGE_DB;

  $s = mysql_query("SELECT * FROM results_2008_candidates");

  while($r = mysql_fetch_array($s)) {
    $name = $r['nume'];
    $id = $r['id'];
    $idperson = $r['idperson'];

    if ($FLAG_CAN_CHANGE_DB == true) {
      mysql_query(
        "UPDATE results_2008 ".
        "SET idperson={$idperson} ".
        "WHERE idcandidat={$id}");
      info("Updated {$id} -> {$idperson}");
      
      mysql_query(
        "UPDATE results_2008_agg ". 
        "SET idperson_winner = {$idperson} ".
        "where winnerid = {$id}");
      mysql_query(
        "UPDATE results_2008_agg ". 
        "SET idperson_runnerup = {$idperson} ".
        "where runnerupid = {$id}");
    }
  }
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php
fixResults2008Tables();

include("../_bottom.php");
?>
</body>
</html>
