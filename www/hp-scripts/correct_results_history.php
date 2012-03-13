<?php
include("../_top.php");

$s = mysql_query(
  "select r.idperson, h.what ".
  "from results_2008 as r ".
  "left join people_history as h ".
      "on h.idperson = r.idperson and h.what='results/2008'");

while ($r = mysql_fetch_array($s)) {
  echo "{$r['idperson']} - {$r['what']}";
  if ($r['what'] == "") {
    mysql_query("insert into people_history(idperson, what, url) ".
                "values({$r['idperson']}, 'results/2008', '')");
    
  }
  echo '<br>';
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