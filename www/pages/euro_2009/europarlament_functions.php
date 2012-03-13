<?php

/**
 * Print some summaries per parties for the elections?
 */
function printPartySummary() {
  // First, select the parties that are participating.
  $s = mysql_query(
    "SELECT e.idparty, p.name, p.name, ".
           "count(*) as c, avg(e.birthday) as average_bday ".
    "FROM euro_2009_candidates AS e ".
    "LEFT JOIN parties AS p ON p.id = e.idparty ".
    "GROUP BY e.idparty");

  while ($r = mysql_fetch_array($s)) {
    echo "". ucwords($r['name']) . ", {$r['c']} candidați";

    $age = round((time() * 1000 - $r['average_bday']) /
                 (1000 * 60 * 60 * 24 * 365));
    echo ", media de vârstă <b>$age de ani</b>.";

    echo "<br>";
  }
}


/**
 * Print some summaries per parties for the elections?
 */
function printPartyDetailed() {
  global $_GET;

  // First, select the parties that are participating.
  $s = mysql_query(
    "SELECT e.idparty, p.name, p.name, ".
           "count(*) as c, avg(e.birthday) as average_bday ".
    "FROM euro_2009_candidates AS e ".
    "LEFT JOIN parties AS p ON p.id = e.idparty ".
    "GROUP BY e.idparty ".
    "ORDER BY c DESC");

  echo <<<END
  <div class="small">
Ce înseamnă iconițele din dreptul fiecăruia?
<div style="padding-left:15px">
<img src="images/icon_catavencu_2008.png"> - Prezent pe lista Candidaților Incompatibili din Academia Cațavencu.
<br><img src="images/icon_results_2008.png"> - A candidat la alegerile parlamentare din Noiembrie 2008.
<br><img src="images/icon_qvorum_2009_0.png" align=absmiddle>
<img src="images/icon_qvorum_2009_1.png" align=absmiddle>
<img src="images/icon_qvorum_2009_2.png" align=absmiddle>
<img src="images/icon_qvorum_2009_3.png" align=absmiddle> - A avut o contribuție <i>slabă</i>, <i>medie</i>, <i>bună</i> sau <i>deosebită</i> în raportul Qvorum asupra activității europarlamentarilor.
<br><img src="images/icon_euro_parliament_2007.png"> - A fost Europarlamentar.
</div></div><br>
END;

  echo "<div>";
  if ($_GET['showall'] != '1') {
    echo "Mai jos sunt listele cu primii 11 candidați de pe fiecare listă.
         Dacă doriți să vedeți listele integrale,
         <a href=?c=alegeri+europarlamentare+2009&cid=10&sid=0&showall=1>
         click aici</a>.<br><br>";
  }
  echo "<table>";
  $count = 0;

  while ($r = mysql_fetch_array($s)) {
    if ($count++ % 2 == 0) {
      echo "<tr>";
    }
    echo "<td width=50% valign=top>";
    echo ucwords($r['name']) . "</a>, {$r['c']} candidați";

    $age = round((time() * 1000 - $r['average_bday']) /
                 (1000 * 60 * 60 * 24 * 365));
    echo ", media de vârstă <b>$age de ani</b>.";

    echo "<div class=euro_party_info>";
    // Print the candidates
    $sql =
        "SELECT e.idperson, e.name, e.occupation, h1.what as wh1, ".
                "h2.what as wh2, h3.what as wh3, h4.what as wh4 ".
        "FROM euro_2009_candidates as e ".
        "LEFT JOIN people_history AS h1 ".
          "ON e.idperson = h1.idperson AND h1.what='catavencu/2008' ".
        "LEFT JOIN people_history AS h2 ".
          "ON e.idperson = h2.idperson AND h2.what='results/2008' ".
        "LEFT JOIN people_history AS h3 ".
          "ON e.idperson = h3.idperson AND h3.what='euro_parliament/2007' ".
        "LEFT JOIN people_history AS h4 ".
          "ON e.idperson = h4.idperson AND h4.what='qvorum/2009' ".
        "WHERE idparty = {$r['idparty']} ".
        "ORDER BY position ASC";
    if ($_GET['showall'] != '1') {
      $sql .= " LIMIT 0, 11";
    }
    $sc = mysql_query($sql);
    $i = 1;
    echo "<table>";

    $num = 0;
    $sum = 0;

    while ($rc = mysql_fetch_array($sc)) {
      echo "<tr>";
      echo "<td>" . $i++ . ".</td>";

      $name = moveFirstNameLast(ucwords(strtolower_ro($rc['name'])));
      $idperson = $rc['idperson'];

      echo "<td><a href=\"?name=" . urlencode($name) . "&cid=9&".
           "id=$idperson\">" .$name. "</a>";

      echo "</td><td align=right>";
      echo getIconHtml($rc['wh1'], $idperson);
      echo getIconHtml($rc['wh2'], $idperson);
      echo getIconHtml($rc['wh3'], $idperson);
      echo getIconHtml($rc['wh4'], $idperson);

      // ------------ TEMP TO COMPUTE SOME STATS
      if ($rc['wh2'] != "") {
        //$num++;
        //$sum += getPercentFromResults($rc['idperson']);
      }
      // ------------

      echo "</td>";

      echo "<td><span class=small>{$rc['occupation']}</span></td>";
    }
    echo "</table>";
    if ($num) {
      //echo "Average ".($sum / $num)." for $num.";
    }
    echo "</div>";
    if ($_GET['showall'] != '1') {
      echo "<a href=?c=alegeri+europarlamentare+2009&cid=10&sid=0&showall=1>
      Vezi lista întreagă...</a><br><br>";
    } else {
      echo "<a href=?c=alegeri+europarlamentare+2009&cid=10&sid=0>
      Vezi doar primii 11 din fiecare listă...</a><br><br>";
    }
    echo "</td>";
  }
  echo "</tr></table>";
}

// --------------TEMP:
function getPercentFromResults($id) {
  $s = "
    SELECT r.voturi, a.total
    FROM results_2008 AS r
    LEFT JOIN results_2008_agg AS a
      ON a.college = r.colegiu
    WHERE r.idperson = $id
    ";
  $sql = mysql_query($s);
  if ($r = mysql_fetch_array($sql)) {
    return 100 * $r['voturi'] / $r['total'];
  }
}


/**
 * Given an object with the number of votes for each party, return an object
 * with the number of seats that each party gets, given the DHondt system.
 *
 * @return {Object} An object with number of seats for each party that went
 *     into the system in the object sent as a parameter.s
 */
function simulateDHondtSystem($votes) {
  global $VOT_PRESENCE;
  // First of all, if the values are under 100, normalize and really make them
  // into votes.
  $sum = 0;
  foreach ($votes as $p=>$v) {
    $sum += $v;
  }
  if ($sum < 500) {
    // Assume a default of 6 milion votes?
    $total = 18646274 * $VOT_PRESENCE / 100;
    foreach ($votes as $p=>$v) {
      $votes[$p] = $total * $v / $sum;
    }
  } else {
    $total = $sum;
  }

  $seats = array();
  $totalSeats = 33;

  // Deal with the independents
  if ($votes['pa'] / $total > 0.03) {
    $seats['pa'] = 1;
    $totalSeats--;
  }
  $votes['pa'] = 0;
  if ($votes['pb'] / $total > 0.03) {
    $seats['pb'] = 1;
    $totalSeats--;
  }
  $votes['pb'] = 0;


  // Eliminate the parties that don't make the electoral 5%
  foreach ($votes as $p=>$v) {
    if ($v / $total < 0.05) {
      $votes[$p] = 0;
    }
  }

  $quotients = array();
  foreach ($votes as $p=>$v) {
    $quotients[$p] = $v;
  }
  // Ok, now we have number of votes for each party, 33 total seats.
  for ($i = 0; $i < $totalSeats; $i++) {
    $maxp = 'p1';
    foreach ($quotients as $p=>$v) {
      $maxp = $quotients[$maxp] < $v ? $p : $maxp;
    }
    //echo $quotients[$maxp] .",". $maxp ."<br>";
    // now split by the number of seats.
    $s = $seats[$maxp] ? $seats[$maxp] + 1 : 1;

    $seats[$maxp] = $s;
    $quotients[$maxp] = $votes[$maxp] / ($s + 1);
  }

  return $seats;
}

/**
 * Tests whether there are values in the URL for the party percentage or
 * votes. If that is true, it simulates the elections system and then
 * it prints the people that would get elected.
 * @return {boolean} True if an election was simulated, false otherwise.
 *     This will be used to decide whether we will still display all the
 *     candidates or just the elected ones.
 */
function maybeDisplaySimulationResults() {
  global $_GET;
  global $VOT_PRESENCE;
  $v = getSimulationSystemValuesFromGet();

  if ($v != null) {
    $seats = simulateDHondtSystem($v);
    echo "Cu aceste procente și o prezență generală la vot ".
         "de $VOT_PRESENCE%, aceștia ar fi europarlamentarii:";
    echo "<table cellspacing=5><tr bgcolor=\"#EEEEEE\"><td></td>".
         "<td>Nume</td>";
    echo "<td></td><td></td><td></td><td></td>".
         "<td>Partid</td>";
    $count = 1;

    foreach ($seats as $p=>$v) {
      $idparty = substr($p, 1);
      switch ($idparty) {
        case "a": $idparty = "10 AND e.idperson=3336 "; break;
        case "b": $idparty = "10 AND e.idperson=3335 "; break;
      }
      $s = mysql_query(
        "SELECT e.name, e.idperson, e.occupation, e.birthday, ".
               "p.name as pname, h4.what as wh4, ".
               "h1.what as wh1, h2.what as wh2, h3.what as wh3 ".
        "FROM euro_2009_candidates AS e ".
        "LEFT JOIN parties AS p ON p.id = e.idparty ".
        "LEFT JOIN people_history AS h1 ".
          "ON e.idperson = h1.idperson AND h1.what='catavencu/2008' ".
        "LEFT JOIN people_history AS h2 ".
          "ON e.idperson = h2.idperson AND h2.what='results/2008' ".
        "LEFT JOIN people_history AS h3 ".
          "ON e.idperson = h3.idperson AND h3.what='euro_parliament/2007' ".
        "LEFT JOIN people_history AS h4 ".
          "ON e.idperson = h4.idperson AND h4.what='qvorum/2009' ".
        "WHERE idparty=$idparty ".
        "ORDER BY position ASC ".
        "LIMIT 0, {$seats[$p]}");

      if ($idparty != "10 AND e.idperson=3336 " &&
          $idparty != "10 AND e.idperson=3335 ") {
        echo "<tr bgcolor=#FAFAFA>";
        echo "<td colspan=7 align=center>" . getPartyNameForId($idparty) ." - {$seats[$p]} locuri</td>";
      }

      while ($r = mysql_fetch_array($s)) {
        echo "<tr>";
        echo "<td align=right>". $count++ ."</td>";

        $name = moveFirstNameLast(ucwords(strtolower_ro($r['name'])));
        echo "<td><a href=\"?name=".urlencode($name).
             "&cid=9&id={$r['idperson']}\">$name</a>";
        $age = round((time() * 1000 - $r['birthday']) /
                     (1000 * 60 * 60 * 24 * 365));
        echo ", $age ani";

        $idperson = $r['idperson'];
        echo "</td>";
        echo "<td align=right>" . getIconHtml($r['wh1'], $idperson) ."</td>";
        echo "<td align=right>" . getIconHtml($r['wh2'], $idperson) ."</td>";
        echo "<td align=right>" . getIconHtml($r['wh3'], $idperson) ."</td>";
        echo "<td align=right>" . getIconHtml($r['wh4'], $idperson) ."</td>";

        //echo "<td><span class=\"small gray\">{$r['occupation']}</span></td>";

        echo "<td>{$r['pname']}</td>";
      }

    }
    echo "</table>";
    return true;
  }

  return false;
}

function getIconHtml($what, $idperson) {
  if ($what != "") {
    $name = str_replace("/", "_", $what);
    switch($name) {
      case "catavencu_2008":
        $alt = "Lista cațavencu a candidaților pătați";
        break;
      case "results_2008":
        $alt = "Candidat parlamentare 2008";
        break;
      case "euro_parliament_2007":
        $alt = "Europarlamentar 2007-2009";
        break;
      case "qvorum_2009":
        $alt = "Raportul Qvorum 2009";
        $s = mysql_query("SELECT score FROM euro_parliament_2007_qvorum
                          WHERE idperson=$idperson");
        $r = mysql_fetch_array($s);
        $name .= "_" . $r['score'];
        switch($r['score']) {
          case 0: $alt .= " - contribuție slabă"; break;
          case 1: $alt .= " - contribuție medie"; break;
          case 2: $alt .= " - contribuție bună"; break;
          case 3: $alt .= " - contribuție deosebită"; break;
        }
        break;
    }
    return "<a href=?cid=9&id=$idperson>".
           "<img src=\"images/icon_$name.png\" hspace=2 ".
           "alt=\"$alt\" title=\"$alt\" border=0></a>";
  }
  return "";
}


/**
 * Extract whatever you can from the URL and put them in a hash.
 */
function getSimulationSystemValuesFromGet() {
  global $_GET;

  $values = array();

  $s = mysql_query(
    "SELECT distinct(idparty) FROM euro_2009_candidates ".
    "GROUP BY idparty");
  $any = false;

  while ($r = mysql_fetch_array($s)) {
    $var = $r['idparty'];

    if ($_GET["p$var"] && is_numeric($_GET["p$var"])) {
      $values["p$var"] = $_GET["p$var"];
      $any = true;
    } else {
      $values["p$var"] = "";
    }

  }

  $values["pa"] = $_GET["pa"] && is_numeric($_GET["pa"]) ? $_GET["pa"] : "";
  $values["pb"] = $_GET["pb"] && is_numeric($_GET["pb"]) ? $_GET["pb"] : "";

  $sum = 0;
  foreach ($values as $p=>$v) {
    $sum += $v;
  }
  if ($sum > 10 && $sum < 500 && ($sum < 99.8 || $sum > 100.2)) {
    // normalize to 100
    foreach ($values as $p=>$v) {
      $values[$p] = floor(10000 * $v / $sum) / 100;
    }
  }

  return $any ? $values : null;
}

?>
