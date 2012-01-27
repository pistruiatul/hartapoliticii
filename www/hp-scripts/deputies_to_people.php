<?
require("../_top.php");
require("../hp-includes/people_lib.php");

$FLAG_CAN_CHANGE_DB = true;

// Functions go here.
function deputiesArePeopleToo() {
  $s = mysql_query("SELECT * FROM deputies");

  while($r = mysql_fetch_array($s)) {
    $name = $r['name'];
    $did = $r['id'];

    $persons = getPersonsByName($name);
    // If I reached this point, I know for sure I either have one
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $r['name']);
    } else {
      $person = $persons[0];
    }
    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "cdep/2004", "http://www.cdep.ro/pls/" .
      "parlam/structura.mp?idm=" . $r['idm'] . "&cam=2&leg=2004");

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database.
    mysql_query(
      "UPDATE deputies SET idperson=$person->id WHERE id=$did");

    mysql_query(
      "UPDATE belongs ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE belong_agg ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE proponents ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE video ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE votes ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE votes_agg ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");

    mysql_query(
      "UPDATE away_times ".
      "SET idperson=$person->id ".
      "WHERE iddepsen=$did AND chamber=2");

    mysql_query(
      "UPDATE alegeritv ".
      "SET idperson=$person->id ".
      "WHERE iddep=$did");
  }
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<pre>
<?
deputiesArePeopleToo();

include("../_bottom.php");
?>
</html>
