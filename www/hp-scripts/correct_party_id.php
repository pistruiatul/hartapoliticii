<?
include("../_top.php");

$s = mysql_query("select id, name from results_2008_parties");

while ($r = mysql_fetch_array($s)) {
  echo 'Updating ' . $r['id'] . ' - ' . $r['name'] . "<br>";
  mysql_query("update senat_2008 set idpartid = " . $r['id'] . ", partid = '". 
      strtolower($r['name']) ."' where partid='" . $r['name'] . "'");
      
  mysql_query(
    "update results_2008_allocated ".
    "set idpartid = " .$r['id']. ", partid = '" .strtolower($r['name']) ."' ".
    "where partid='" . $r['name'] . "'");
}

/*
$s = mysql_query("select colegiu from results_2008 group by colegiu");
while ($r = mysql_fetch_array($s)) {
  mysql_query("update results_2008 set colegiu = concat('D', '" . 
      $r['colegiu'] . "') where colegiu = '" . $r['colegiu'] . "'");
}
*/

// Move my candidates from senat_2008 to the results_2008_candidates table
/*
$s = mysql_query('select nume, colegiu from senat_2008');
while ($r = mysql_fetch_array($s)) {
  $ids = mysql_query("select id from results_2008_candidates where " .
      "nume = '" . $r['nume'] . "' and college = '" . $r['colegiu'] . "'");
  $idr = mysql_fetch_array($ids);
  
  mysql_query("update senat_2008 set idcandidat = " . $idr['id'] . " where " .
      "nume = '" . $r['nume'] . "' and colegiu = '" . $r['colegiu'] . "'");
}
*/


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
  
  
</body>
</html>
<?
include("../_bottom.php");
?>