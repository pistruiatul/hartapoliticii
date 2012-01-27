<?

echo "<div>";

$s = mysql_query("
  SELECT * FROM people_facts 
  WHERE idperson={$person->id} AND attribute='resume'");
$r = mysql_fetch_array($s);
echo $r['value'];

echo "</div>";

?>