<?
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT e.id, e.position, p.long_name, p.name " .
  "FROM euro_2009_candidates as e ".
      "LEFT JOIN parties as p ON p.id = e.idparty ".
  "WHERE e.idperson = $person->id ";

$se = mysql_query($sql);
$re = mysql_fetch_array($se);

echo "<div>";
echo "<a href=http://www.europarl.europa.eu/elections2009/default.htm?language=ro><img src=http://www.europarl.europa.eu/eplive/expert/photo/20090209PHT48834/th_pict_20090209PHT48834.jpg width=100 align=right border=0></a>";
// Print the party belonging during his senator years
echo "Candidează pe <a href=\"?c=alegeri+europarlamentare+2009&".
     "cid=10&sid=0\">".
     "lista ".ucwords($re['name'])."</a> pe poziția <b>".
     "<font color=darkred size=+1>" .
     $re['position'] . "</font></b>.";
echo " Vezi <a href=f/{$person->id}_euro_2009_1.pdf>".
     "declarația de avere și interese</a> ".
     "<a href=f/{$person->id}_euro_2009_1.pdf>".
     "<img src=images/icon_pdf.gif border=0></a>.";

echo "<div class=small>Candidatul ";
// Select the next and previous ID's
$s = mysql_query(
  "SELECT id, idperson, name FROM euro_2009_candidates ".
  "WHERE id = ".($re['id'] - 1)." OR id = ".($re['id'] + 1));

while ($r = mysql_fetch_array($s)) {
  $nume = urlencode(strtolower_ro(moveFirstNameLast($r['name'])));

  if ($r['id'] == $re['id'] - 1) {
    echo "<a href=\"?name=$nume&cid=9&id={$r['idperson']}\">anterior</a> / ";
  }
  if ($r['id'] == $re['id'] + 1) {
    echo "<a href=\"?name=$nume&cid=9&id={$r['idperson']}\">următor</a>";
  }
}
echo ". ";

?>
Mai multe despre alegeri
<a href="?c=alegeri+europarlamentare+2009&cid=10">aici</a>.</div>
<?
echo "</div>";

?>