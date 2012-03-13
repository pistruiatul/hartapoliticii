<?php
include("../_top.php");

$s = mysql_query(
  "select rp.id, rp.name, p.id as realid, long_name ". 
  "from results_2008_parties as rp ".
  "left join parties as p on p.long_name = rp.name");

while ($r = mysql_fetch_array($s)) {
  echo 'Updating ' . $r['id'] . ' - ' . $r['name'];

  if ($r['realid']) {
    echo ' <b>got this</b>';

    mysql_query("UPDATE results_2008 SET idpartid={$r['realid']} ".
                "WHERE partid='{$r['long_name']}'");
    
    mysql_query("UPDATE results_2008_allocated SET idpartid={$r['realid']} ".
                "WHERE partid='{$r['long_name']}'");
  } else {
    echo ' <b>should insert</b>';
    
    mysql_query(
      "insert into parties(name, long_name) ".
      "values('', '{$r['name']}')");
  }
  echo '<br>';
}

$s = mysql_query("SELECT idperson, idpartid FROM results_2008 WHERE 1");
while ($r = mysql_fetch_array($s)) {
  mysql_query("UPDATE results_2008_candidates SET idpartid={$r['idpartid']} ".
              "WHERE idperson={$r['idperson']}");
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
  
  
</body>
</html>
<?php
include("../_bottom.php");
?>