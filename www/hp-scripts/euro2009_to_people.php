<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

// Functions go here.
function candidatesArePeopleToo() {
  global $FLAG_CAN_CHANGE_DB;

  $s = mysql_query(
    "SELECT e.id, e.name, e.idparty, p.name as pname 
    FROM euro_2009_candidates AS e
    LEFT JOIN parties AS p ON p.id = e.idparty 
    where idperson=0
    ");

  while($r = mysql_fetch_array($s)) {
    $name = $r['name'];
    $idString = '' . $r['pname'] . '';

    $persons = getPersonsByName($name, $idString, infoFunction);
    // If I reached this point, I know for sure I either have one 
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $r['name']);
    } else {
      $person = $persons[0];
    }

    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "euro/2009",
 "http://ro.wikipedia.org/wiki/Alegeri_pentru_Parlamentul_European_%C3%AEn_Rom%C3%A2nia,_2009", 1244349442);

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database, from the previous ID that was
    // the senator's ID in the senators table.

    if ($FLAG_CAN_CHANGE_DB) {
      mysql_query(
        "UPDATE euro_2009_candidates " .
        "SET idperson={$person->id} " .
        "WHERE id=" . $r['id']);
    }
  }
  printJsCommitCookieScript();
}

function infoFunction($person, $idString) {
  $s = mysql_query(
    "select p.name from people ".
    "left join people_facts as facts ".
        "on facts.idperson = people.id AND facts.attribute = 'party' ".
    "left join parties as p ".
        "on p.id = facts.value ".
    "where people.id = {$person->id} ".
    "group by p.id" );
  while ($r = mysql_fetch_array($s)) {
    $str .= $r['name'];
  }

  if ($str == $idString) {
    return "match ok";
  }

  return $str;
}


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php
candidatesArePeopleToo();

include("../_bottom.php");
?>
</body>
</html>
