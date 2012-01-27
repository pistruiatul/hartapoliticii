<?
require("../_top.php");
require("../hp-includes/people_lib.php");

// Functions go here.
function candidatesArePeopleToo() {
  global $FLAG_CAN_CHANGE_DB;

  $s = mysql_query(
    "SELECT e.id, e.name, e.text 
    FROM euro_parliament_2007_qvorum AS e
    WHERE idperson=0
    ");

  while($r = mysql_fetch_array($s)) {
    $name = $r['name'];

    $persons = getPersonsByName($name, $name, infoFunction);
    // If I reached this point, I know for sure I either have one 
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $r['name']);
    } else {
      $person = $persons[0];
    }

    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "qvorum/2009",
                     "http://qvorum.ro/ViewAnalyse.aspx?analyseID=13", 
                     1242508980);

    if (strpos($r['text'], "o contribuţie slabă") > 0) { $score = 0; } 
    else if (strpos($r['text'], "o contribuţie medie") > 0) { $score = 1; }
    else if (strpos($r['text'], "o contribuţie bună") > 0) { $score = 2; }
    else if (strpos($r['text'], "o contribuţie deosebită") > 0) { $score = 3;} 

    mysql_query("UPDATE euro_parliament_2007_qvorum SET score=$score 
                 WHERE id={$r['id']}");

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database, from the previous ID that was
    // the senator's ID in the senators table.

    if ($FLAG_CAN_CHANGE_DB) {
      mysql_query(
        "UPDATE euro_parliament_2007_qvorum " .
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
<?
candidatesArePeopleToo();

include("../_bottom.php");
?>
</body>
</html>
