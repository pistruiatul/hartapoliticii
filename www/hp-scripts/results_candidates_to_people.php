<?
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 200;

// Functions go here.
function candidatesArePeopleToo() {
  global $FLAG_CAN_CHANGE_DB;

  $s = mysql_query("SELECT * FROM results_2008_candidates WHERE idperson=0");

  while($r = mysql_fetch_array($s)) {
    $name = $r['nume'];
    $sid = $r['id'];

    list($cod, $judet) = split(" ", $r['college']);
    $idString = "$judet-$cod";

    if ($judet == 'Strainatate') {
      $url = "http://www.thinkopolis.eu/colegiu/Diaspora-$cod";
    } else {
      $jsel = mysql_query("SELECT id FROM counties WHERE name='$judet'");
      if ($rsel = mysql_fetch_array($jsel)) {
        $url = "http://alegeri-2008.ro/candidati/x-{$rsel['id']}/";
      }
    }

    // Let's attempt to create an id string. Best guess we have for these
    // people is the info-alegeri site. We would like to avoid linking to the
    // BEC pdf's, even though those would be the best ones.

    $persons = getPersonsByName($name, $idString, getExistingPersonInfo);
    // If I reached this point, I know for sure I either have one 
    // or zero matches, there are no ambiguities.
    if (count($persons) == 0) {
      $person = addPersonToDatabase($name, $name);
    } else {
      $person = $persons[0];
    }

    // First and foremost, attempt to add this to the person's history.
    addPersonHistory($person->id, "results/2008", $url);

    // Locate these people and update them in several tables now.
    // We're mainly interested in updating the ID to the new ID that
    // will be in the People database, from the previous ID that was
    // the senator's ID in the senators table.
    if ($FLAG_CAN_CHANGE_DB == true) {
      mysql_query(
        "UPDATE results_2008_candidates ".
        "SET idperson={$person->id} ".
        "WHERE id={$sid}");
    }
  }
  printJsCommitCookieScript();
}


function getExistingPersonInfo($person, $idString) {
  $existing = getHistoryString($person);

  if (strrpos($existing, $idString) === FALSE) {
    $s = mysql_query(
      "SELECT cand.college, parties.name ".
      "FROM results_2008_candidates AS cand ".
        "LEFT JOIN results_2008_parties AS parties ".
            "ON parties.id = cand.idpartid ".
      "WHERE nume = '{$person->displayName}'");
    $r = mysql_fetch_array($s);
    return $existing . " ({$r['name']})";
  }

  return 'match ok';
}


/**
 * If we define this funciton it will simply be used by the people_lib to
 * allow us to print more details about a person when that person is 
 * identified as a new person.
 */
function shouldAddDetailsFunction($name) {
  // For now I will just print the people that I already know are in this
  // college. Maybe in the future this method should be fancier?

  // What is the college that this guy is running for?
  $s = mysql_query(
    "SELECT cand.college, parties.name ".
    "FROM results_2008_candidates AS cand ".
      "LEFT JOIN results_2008_parties AS parties ".
          "ON parties.id = cand.idpartid ".
    "WHERE nume = '$name'");
  $r = mysql_fetch_array($s);

  list($cod, $judet) = split(" ", $r['college']);
  $college = "$judet-$cod";

  $jsel = mysql_query("SELECT id FROM counties WHERE name='$judet'");
  if ($rsel = mysql_fetch_array($jsel)) {
    $url = "http://alegeri-2008.ro/candidati/x-{$rsel['id']}/";
  }
  info("           ({$r['name']})");
  info("  În colegiul <a href=$url>$college</a>");

  $s = mysql_query(
    "SELECT people.name, people.display_name ".
    "FROM alegeri_2008_candidates AS cand ".
    "LEFT JOIN alegeri_2008_colleges AS colleges ".
      "ON colleges.id = cand.college_id ".
    "LEFT JOIN people ON people.id = cand.idperson ".
    "WHERE colleges.url LIKE '%/$college'");
  while ($r = mysql_fetch_array($s)) {
    info("    " . $r['display_name'] . " " . 
         getResolveString($name, $r['name'], "&lt;="));
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
