<?php
// We know that the person we are talking about is $person.

$sql = "SELECT colegiu FROM results_2008 WHERE idperson = {$person->id}";
$r = mysql_fetch_array(mysql_query($sql));
$college = $r['colegiu'];

$sql =
  "SELECT college, idperson_winner, idperson_runnerup ".
  "FROM results_2008_agg ".
  "WHERE college = '{$college}'";

$r = mysql_fetch_array(mysql_query($sql));
$idpersonWinner = $r['idperson_winner'];
$idpersonRunnerup = $r['idperson_runnerup'];


$sql =
  "SELECT people.id, people.display_name, people.name, res.voturi, ".
      "parties.name AS party, cand.reason, cand.difference, ".
      "parties.minoritati ".
  "FROM results_2008 AS res ".
  "LEFT JOIN people ON people.id = res.idperson ".
  "LEFT JOIN parties ".
      "ON parties.id = res.idpartid ".
  "LEFT JOIN results_2008_candidates AS cand ".
      "ON cand.idperson = people.id ".
  "WHERE colegiu = '$college' ".
  "ORDER BY voturi DESC";

$s = mysql_query($sql);

echo "Rezultate <b>$college</b>";
echo "<table width=100% cellpadding=2>".
     "<tr bgcolor=\"#EEEEEE\"><td>Candidat</td>".
     "<td>Partid</td>".
     "<td>Voturi</td>".
     "</tr>";
$hasHidden = false;
while ($r = mysql_fetch_array($s)) {
  $visibility = $r['minoritati'] == 1 ? 'minoritati' : '';
  $hasHidden = $hasHidden || $r['minoritati'] == 1;

  if ($r['id'] == $idpersonWinner) {
    echo "<tr class=winnerrow >";
  } else if ($r['id'] == $person->id) {
    echo "<tr class=personrow>";
  } else {
    echo "<tr class=\"othersrow $visibility\" name=$visibility>";
  }
  $cand_name = str_replace(' ', '+', $r['name']);

  if ($r['id'] == $person->id) {
    echo "<td>{$r['display_name']}</td>";
  } else {
    echo "<td><a href='?name={$cand_name}'>{$r['display_name']}</a></td>";
  }
  echo "<td>" . $r['party']. "</td>";
  echo "<td>{$r['voturi']}";
  echo $r['id'] == $idpersonWinner ?
       ", câștigător</td>" :
       ", <span class=\"small gray\">cu {$r['difference']} ".
           "{$r['reason']}</span></td>";

  echo "</tr>\n";
}
echo "</table>";

if ($hasHidden) {
  ?>
  <script>
  function showMinoritati() {
    var rows = document.getElementsByName('minoritati');
    for (var i = 0; i < rows.length; i++) {
      rows[i].className = rows[i].className.indexOf('minoritati') >= 0 ?
                          '' :
                          'minoritati';
    }
  }
  </script>
  <?php
  echo "<a href=\"javascript:showMinoritati();\">".
       "<span id=min_link>+ minorități</span></a>";
}

?>