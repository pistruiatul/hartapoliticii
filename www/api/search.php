<?php
include_once('../secret/api_key.php');

include_once('../_top.php');
include_once('../functions.php');
include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/party_class.php');


/**
 * @param {Person} $person
 * @return array
 */
function getOutputObjectForPerson($person) {
  $p = array();
  $p["id"] = $person->id;
  $p["name"] = $person->displayName;
  $p["party"] = $person->getFact("party");

  if ($p["party"]) {
    $party = new Party($p["party"]);
    $p["party_name"] = $party->name;
  } else {
    $p["party_name"] = NULL;
  }
  $p["college_name"] = $person->getCollegeName();
  return $p;
}


$query = trim($_GET['q']);
$persons = search($query);

// If I reached this point, I know for sure I either have one
// or zero matches, there are no ambiguities.

$output = array();

// If the first person is an exact match, just return that one, it means we
// have identified the right person and it makes no sense to return the
// other less strong matches.

if (count($persons) > 0 && personQueryIsNavigational($query, $persons[0])) {
  $output[] = getOutputObjectForPerson($persons[0]);
} else {
  for ($i = 0; $i < count($persons); $i++) {
    $output[] = getOutputObjectForPerson($persons[$i]);
  }
}

echo json_encode($output);
?>
