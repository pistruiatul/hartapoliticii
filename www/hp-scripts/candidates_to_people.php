<?
require("../_top.php");
require("../hp-includes/people_lib.php");

$FLAG_CAN_CHANGE_DB = true;

// Functions go here.
function candidatesArePeopleToo() {
  $s = mysql_query("SELECT * FROM candidates");

  while($r = mysql_fetch_array($s)) {
    $name = $r['name_cleaned'];
    $sid = $r['id'];

    if (strpos($r['url'], "http://") === 0) {
      $url = $r['url'];
    } else {
      $url = 'http://www.alegeri-2008.ro' . $r['url'];
    }
    $idString = '<a href=' . $url . '>alegeri2008</a>';

    $persons = getPersonsByName($name, $idString);
    // If I reached this point, I know for sure I either have one 
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $r['name_cleaned']);
    } else {
      $person = $persons[0];
      info("Found      {" . $name . "}");
    }

    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "alegeri/2008", $url);

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database, from the previous ID that was
    // the senator's ID in the senators table.
    
    mysql_query(
      "UPDATE candidates " .
      "SET idperson=$person->id " .
      "WHERE id=" . $r['id']);
  }
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
