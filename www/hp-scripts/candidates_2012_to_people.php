<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;
$FLAG_CAN_CHANGE_DB = true;

function deleteAllContentFirst() {
  mysql_query("DELETE FROM results_2012 WHERE 1");
  mysql_query("DELETE FROM people_history WHERE what='results/2012'");
}


function extractNameAndParty($person) {
  preg_match("/(.*) [(](.*)[)]/", $person, $matches);
  return array($matches[1], $matches[2]);
}


function getPartyId($partyShortName) {
  if ($partyShortName == 'PDL') $partyShortName = "PD-L";
  if ($partyShortName == 'Forta Civica') $partyShortName = "FC";
  if ($partyShortName == 'Miscarea Verzilor') $partyShortName = "MV";
  if ($partyShortName == 'Partidul Verde') $partyShortName = "PVE";
  if ($partyShortName == 'Alianta Socialista') $partyShortName = "PAS";

  $s = mysql_query("SELECT id FROM parties WHERE name='{$partyShortName}'");
  $r = mysql_fetch_array($s);
  return $r['id'];
}

function addCandidateToCollege($college, $candidate, $party) {
  if ($candidate == "") return;

  $context = "+ {{$college}} ({$party}) {$candidate}";
  info($context);

  $results = getPersonsByName($candidate, $context, infoFunction);

  if (count($results) == 0) {
    $person = addPersonToDatabase($candidate, $candidate);

  } else {
    $person = $results[0];
    info("  - person {{$candidate}} id : [" . $person->id . "]");
  }

  $partyId = getPartyId($party);
  if (!$partyId > 0) {
    die('party id gone wrong');
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
      values({$person->id}, 'results/2012', 'http://goo.gl/64gsf', 1355032800)
  ");
}


function importFile($file_name) {
  global $startWith;

  $data = file_get_contents($file_name);
  $json = json_decode($data, true);

  info("[---------------- starting with {$startWith} --------------------");

  for ($i = $startWith; $i < count($json); $i++) {
    $college = $json[$i];

    $college_name = ucwords(strtolower("D" . (float)$college["coldep"] . " " .
                                       $college["jud_lbl"]));
    info("[---------------------------------------------------]");
    info("[{$i}. {$college_name}]");

    $parts = extractNameAndParty($college["USL"]);
    addCandidateToCollege($college_name, $parts[0], $parts[1]);

    $parts = extractNameAndParty($college["ARD"]);
    addCandidateToCollege($college_name, $parts[0], $parts[1]);

    addCandidateToCollege($college_name, $college["PP_DD"], "PP_DD");
    addCandidateToCollege($college_name, $college["UDMR"], "UDMR");

    $others = explode(", ", $college["Alti_Candi"]);
    foreach ($others as $other_candidate) {
      $other_candidate = trim($other_candidate);
      if ($other_candidate == "") continue;

      $parts = extractNameAndParty($other_candidate);
      addCandidateToCollege($college_name, $parts[0], $parts[1]);
    }

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

$startWith = (int)$_GET['startWith'];

deleteAllContentFirst();

importFile('candidates_2012_cdep.json');

include("../_bottom.php");
?>
</pre>
</body>
</html>
