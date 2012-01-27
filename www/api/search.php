<?
include_once('../secret/api_key.php');

include_once('../_top.php');
include_once('../functions.php');
include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/party_class.php');

$query = trim($_GET['q']);
$persons = search($query);

// If I reached this point, I know for sure I either have one
// or zero matches, there are no ambiguities.

$output = array();

for ($i = 0; $i < count($persons); $i++) {
  $p = array();
  $p["id"] = $persons[$i]->id;
  $p["name"] = $persons[$i]->displayName;
  $p["party"] = $persons[$i]->getFact("party");

  if ($party) {
    $party = new Party($p["party"]);
    $p["party_name"] = $party->name;
  } else {
    $p["party_name"] = NULL;
  }
  $p["college_name"] = $persons[$i]->getCollegeName();
  $output[] = $p;
}

echo json_encode($output);
?>
