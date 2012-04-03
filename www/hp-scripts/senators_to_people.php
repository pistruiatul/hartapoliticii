<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

// The $ambiguities was built after running the script, disambiguating one
// name at a time, when the library would require me to do so.
$ambiguities = array(
  'Ungureanu Vasile Ioan' => 'Ungureanu Vasile Ioan Dănuț',
  'Antonie Stefan' => 'Antonie Stefan-Mihail',
  'Campeanu Radu' => 'Campeanu Radu Anton',
  'Chelaru Ion' => 'Chelaru Ioan Ion',
  'Dincu Vasile' => 'dancu dincu vasile',
  'Dumitrescu Viorel' => 'dumitrescu gheorghe viorel',
  'Fekete Szabo Andras' => 'andras fekete levente szabo',
  'Ion Vasile' => 'Ion Vasile',
  'Iorga Nicolae' => 'iorga marian nicolae',
  'Mardare Radu' => 'catalin mardare radu',
  'Marasescu Niculae' => 'marasescu nicolae niculae',
  'Mereuta Mircea' => 'gheorghe mereuta mircea',
  'Moisuc Viorica' => 'georgeta moisuc pompilia viorica',
  'Moldoveanu Viorel' => 'aurelian moldoveanu viorel',
  'Onaca Dorel' => 'constantin dorel onaca',
  'Pacuraru Paul' => 'anton nicolae pacuraru paul',
  'Prodan Tiberiu' => 'aurelian prodan tiberiu',
  'Radoi Ioan' => 'ioan ion radoi',
  'Solcanu Ioan' => 'ioan ion solcanu',
  'Stragea Cristian' => 'cristian dimitrie stragea',
  'Serbu Gheorghe' => 'gheorghe serbu vergil',
  'Toma Ioan' => 'ioan ion toma',
  'Vargau Ioan' => 'ioan ion vargau',
  'Puskas Valentin' => 'puskas valentin zoltan',
  'Tarle Radu' => 'radu tarle tirle',
  'Geoana Mircea Dan' => 'Geoana Mircea Dan',
  'Popescu Dan Mircea' => 'Popescu Dan Mircea',
  'Popa Nicolae Vlad' => 'Popa Nicolae Vlad'
  );

// Functions go here.
function senatorsArePeopleToo() {
  $s = mysql_query("SELECT * FROM senat_2004_senators");

  while($r = mysql_fetch_array($s)) {
    $name = $r['name'];
    $sid = $r['id'];

    $persons = getPersonsByName($name);
    // If I reached this point, I know for sure I either have one 
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $r['name_diacritics']);
    } else {
      $person = $persons[0];
    }

    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "senat/2004", "http://www.cdep.ro/pls/" .
      "parlam/structura.mp?idm=" . $r['idm'] . "&cam=1&leg=2004");

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database, from the previous ID that was
    // the senator's ID in the senators table.

    // senators() table.
    mysql_query(
      "UPDATE senators SET idperson=$person->id WHERE id=$sid");

    mysql_query(
      "UPDATE senators_belongs ".
      "SET idperson=$person->id ".
      "WHERE idsen=$sid");

    mysql_query(
      "UPDATE senators_belongs_agg ".
      "SET idperson=$person->id ".
      "WHERE idsen=$sid");

    mysql_query(
      "UPDATE votes_senators ".
      "SET idperson=$person->id ".
      "WHERE idsen=$sid");

    mysql_query(
      "UPDATE votes_senators_agg ".
      "SET idperson=$person->id ".
      "WHERE idsen=$sid");
      
    mysql_query(
      "UPDATE away_times ".
      "SET idperson=$person->id ".
      "WHERE iddepsen=$sid AND chamber=1");
  }
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<pre>
<?php
senatorsArePeopleToo();

include("../_bottom.php");
?>
</html>
