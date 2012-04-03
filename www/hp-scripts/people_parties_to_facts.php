<?php
require("../_top.php");

function extractPartyFacts() {
  // Extract from the elections table the last party each candidate
  // ran for. We consider that his/her party for Nov. 2008.

  $s = mysql_query(
    "SELECT idperson, idpartid ".
    "FROM results_2008 AS res ".
    "GROUP BY idperson");

  // Delete all the party affiliations from facts.
  mysql_query("DELETE FROM people_facts WHERE attribute='party'");
  
  while ($r = mysql_fetch_array($s)) {
    $sql = "INSERT INTO people_facts(idperson, attribute, value, time_ms) ".
           "VALUES({$r['idperson']}, 'party', ".
                  "'{$r['idpartid']}', 1228088126000)";
    mysql_query($sql);
  }
  
  // ---------------- Now extract the parties from cdep and senat
  $s = mysql_query(
    "SELECT idperson, idparty FROM cdep_2004_belong_agg");

  while ($r = mysql_fetch_array($s)) {
    $s2 = mysql_query(
      "SELECT * FROM people_facts ".
      "WHERE idperson = {$r['idperson']} AND attribute = 'party'");

    if (mysql_num_rows($s2) == 0) {
      echo "{$r['idperson']} ";
      $sql = "INSERT INTO people_facts(idperson, attribute, value, time_ms) ".
             "VALUES({$r['idperson']}, 'party', ".
                    "'{$r['idparty']}', 1228088126000)";
      mysql_query($sql);
    }
  }
  echo "<br>\n";
  
  // ---------------- Now extract the parties from the senate
  $s = mysql_query(
    "SELECT idperson, idparty FROM senat_2004_belong_agg");

  while ($r = mysql_fetch_array($s)) {
    $s2 = mysql_query(
      "SELECT * FROM people_facts ".
      "WHERE idperson = {$r['idperson']} AND attribute = 'party'");

    if (mysql_num_rows($s2) == 0) {
      echo "{$r['idperson']} ";
      $sql = "INSERT INTO people_facts(idperson, attribute, value, time_ms) ".
             "VALUES({$r['idperson']}, 'party', ".
                    "'{$r['idparty']}', 1228088126000)";
      mysql_query($sql);
    }
  }
  echo "<br>\n";
  
  
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php
extractPartyFacts();

include("../_bottom.php");
?>
</pre>
</body>
</html>
