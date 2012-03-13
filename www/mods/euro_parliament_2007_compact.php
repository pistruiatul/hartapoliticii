<?php
// Prints all the stuff that a guy did that was in 2007-2009
// european parliament.
// We know that the person we are talking about is $person.

$sql =
  "SELECT e.tin, e.tout, e.present, e.total, h.url " .
  "FROM euro_parliament_2007_agg AS e ".
  "LEFT JOIN people_history AS h ".
     "ON h.idperson = $person->id AND h.what='euro_parliament/2007' ".
  "WHERE e.idperson = $person->id ";

$s = mysql_query($sql);
$r = mysql_fetch_array($s);

echo "<div>";

$a = $r['tin'] == 0 ? 1197262800 : $r['tin'];
$b = $r['tout'] == 0 ? 1241668800 : $r['tout'];

$percent = round(100 * $r['present'] / $r['total']);

// Print the party belonging during his senator years
echo "Prezent în Parlamentul European între ". date("M Y", $a) . " și ".
     date("M Y", $b) . ", la <b>{$r['present']} din ".
     "{$r['total']} ședințe</b> ($percent%, media parlamentului este ".
     "de 82%). ";
echo "Mai multe detalii pe <a href=\"{$r['url']}\">pagina ".
     "parlamentului european</a>.";
echo "</div>";

?>
