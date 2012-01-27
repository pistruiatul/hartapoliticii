<?php
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

$FLAG_CAN_CHANGE_DB = true;

function addPerson($name) {
  $persons = getPersonsByName($name);

  // If I reached this point, I know for sure I either have one
  // or zero matches, there are no ambiguities.
  if (count($persons) == 0) {
    $person = addPersonToDatabase($name, $name);
    return $person->id;
  }
  return -1;
}

$name = trim($_GET['name']);
if ($name) {
  $id = addPerson($name);
  echo $id;
  if ($id > 0) {
    mysql_query("UPDATE news_qualifiers SET idperson={$id} WHERE name='$name'");
  }
}

include ('../_bottom.php');
?>
