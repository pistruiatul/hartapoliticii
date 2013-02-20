<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;
$FLAG_CAN_CHANGE_DB = true;

function getCollegeName($county, $senate_college, $cdep_college) {
  if ($senate_college != "") {
    return ucwords("S{$senate_college} {$county}");
  } else {
    return ucwords("D{$cdep_college} {$county}");
  }
}


function getPartyId($party_name) {
  $party_name = str_replace("\"", "", $party_name);

  switch($party_name) {
    case 'UNIUNEA BULGARĂ DIN BANAT - ROMÂNIA':
      $party_name = 'UNIUNEA BULGARĂ DIN BANAT-ROMÂNIA';
      break;

    case 'PARTIDUL POPORULUI - DAN DIACONESCU':
      $party_name = 'PARTIDUL POPORULUI DAN DIACONESCU';
      break;

    case 'INDEPENDENT':
      $party_name = 'candidat independent';
      break;
  }

  $s = mysql_query("
      SELECT * FROM parties
      WHERE long_name = '{$party_name}'
  ");

  if ($r = mysql_fetch_array($s)) {
    return $r['id'];
  } else {
    info("We failed to find [{$party_name}]");
    exit(1);
  }
}


function addVotesInResultsTable($person, $party_name, $college_name, $votes) {
  // If we don't have a row for $person_id + $college_name, add one, it means
  // we're dealing with a minority candidate that has gathered votes all
  // over the place.
  $s = mysql_query("
      SELECT * FROM results_2012
      WHERE idperson = {$person->id} AND colegiu = '{$college_name}'");
  if (mysql_num_rows($s) == 0) {
    // TODO(vivi): Find the ID of the party we are looking for here, and
    // insert a new row in the table.
    $partyId = getPartyId($party_name);

    mysql_query("
        INSERT INTO results_2012(nume, idperson, partid, idpartid, colegiu, voturi)
        values('{$person->displayName}', {$person->id}, '{$party_name}',
               {$partyId}, '{$college_name}', $votes)
    ");
  }

  mysql_query("
      UPDATE results_2012
      SET voturi = {$votes}
      WHERE idperson = {$person->id} AND colegiu = '{$college_name}'
  ");
}


function importFile($file_name) {
  global $startWith;

  $file_handle = fopen($file_name, "r");

  info("[---------------- starting with {$startWith} ----------------]");
  $index = 0;

  while (!feof($file_handle)) {
    $line = fgets($file_handle);

    $index++;
    if ($index < $startWith) {
      continue;
    }

    if (startsWith($line, '#')) {
      echo "comment: " . $line;
      continue;
    }

    $startWith = $index;

    # This is just content, add it up.
    list($name, $party, $circumscription, $county, $senate_college,
        $cdep_college, $all_votes, $all_present, $votes,
        $is_winner, $percent) = explode(",", $line);

    $college_name = getCollegeName($county, $senate_college, $cdep_college);

    $results = getPersonsByName($name, trim($line) .
                                       " [{$college_name}]", infoFunction);
    if (count($results) == 0) {
      // This is probably a minority result, I should add them in the
      // results DB to make sure we have them there as minorities.
      info("[skipped {$name}, probably minority we don't know about]");
      info("{" . trim($line) . "}");

      $person = addPersonToDatabase($name, $name);
    } else {
      $person = $results[0];
    }

    info("person->id = {$person->id} ({$person->name}) has {$votes} votes");

    addVotesInResultsTable($person, $party, $college_name, $votes);

    info("{$index}. ");
  }

  fclose($file_handle);
}


function infoFunction($person, $idString) {
  return $person->name;
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php

$startWith = (int)$_GET['startWith'];
importFile('bec_rezultate_parlamentare_2012_final.csv');

include("../_bottom.php");
?>
</pre>
</body>
</html>
