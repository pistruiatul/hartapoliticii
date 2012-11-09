<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;
$FLAG_CAN_CHANGE_DB = true;



function deleteAllContentFirst() {
  mysql_query("DELETE FROM results_2012 WHERE 1");
  mysql_query("DELETE FROM people_history WHERE what='results/2012'");
}


function getPartyId($partyShortName) {
  if ($partyShortName == 'PDL') $partyShortName = "PD-L";
  if ($partyShortName == 'Forta Civica') $partyShortName = "FC";
  if ($partyShortName == 'Miscarea Verzilor') $partyShortName = "MV";
  if ($partyShortName == 'Partidul Verde') $partyShortName = "PVE";
  if ($partyShortName == 'Alianta Socialista') $partyShortName = "PAS";
  if ($partyShortName == 'PPDD') $partyShortName = "PP_DD";

  $s = mysql_query("SELECT id FROM parties WHERE name='{$partyShortName}'");
  $r = mysql_fetch_array($s);
  return $r['id'];
}


function addCandidateToCollege($college, $candidate, $party, $source) {
  if ($candidate == "") return;

  $context = "+ {{$college}} ({$party}) {$candidate}";
  info($context);

  $results = getPersonsByName($candidate, $context, infoFunction);

  if (count($results) == 0) {
    $person = addPersonToDatabase($candidate, $candidate);

  } else {
    $person = $results[0];
    info("  - person {{$candidate}} id : [" . $person->id . "]");

    // Make sure this matches!
    $s = mysql_query("
        SELECT colegiu, partid
        FROM results_2012_old
        WHERE idperson = {$person->id}");
    $r = mysql_fetch_array($s);

    if ($r) {

      $current_college = ucwords(strtolower(str_replace("-", " ", $r["colegiu"])));

      $exclude_ids = array(1134, 77, 777, 4646, 1676, 3957, 3961, 4512, 2285,
                           2213, 277, 2220, 2228, 4411, 4407, 4401, 4403, 4750,
                           4744, 2344, 4409, 1208, 4405, 4405, 2341, 3145,
                           4473, 4464,
                           176, 2207, 4398, 2244, 3888, 4541, 3897, 3901, 3905);

      if ($current_college != "" && $current_college != $college &&
          !in_array($person->id, $exclude_ids)) {
        info("We might have a problem. [{$current_college}] != [{$college}]");
        die();
      }

      $party = $r["partid"];
    }
  }

  $partyId = getPartyId($party);
  if ($partyId <= 0) {
    die("party id gone wrong {$party} {$partyId}");
  }
  // Now that I have the person, let's populate the database with it.
  // We need two things: One an entry in results_2012, and an entry in
  // people_history so we can display the mod on their own page.

  mysql_query("
      INSERT INTO results_2012(nume, idperson, partid, idpartid, colegiu)
      values('{$candidate}', {$person->id}, '{$party}', $partyId, '{$college}')
  ");

  mysql_query("
      INSERT INTO people_history(idperson, what, url, time)
      values({$person->id}, 'results/2012', '${$source}', 1355032800)
  ");
}


function importFile($file_name) {
  global $startWith;

  $data = file_get_contents($file_name);
  $json = json_decode($data, true);

  info("[---------------- starting with {$startWith} ----------------]");

  for ($i = $startWith; $i < count($json); $i++) {
    $candidate = $json[$i];
    if ($candidate["EXPLICATIE"]) continue;

    $num = (float)$candidate["college"];
    $college_name = ucwords(strtolower("{$candidate["room"]}{$num} {$candidate["county"]}"));

    addCandidateToCollege($college_name, $candidate["name"],
                          $candidate["party"], $candidate["source"]);

    $startWith = $i;
  }
}


function infoFunction($person, $idString) {
  return $person->name . ' ' . $idString;
}


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php

deleteAllContentFirst();

$startWith = (int)$_GET['startWith'];
importFile('candidates_2012_bec.json');

include("../_bottom.php");
?>
</pre>
</body>
</html>
