<?php
include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');
include ('../smarty/Smarty.class.php');


/**
 * Given an id, looks on disk for the tiny picture of this person.
 * Returns the default non-photo for people that don't have a tiny picture.
 * @param $id
 * @return unknown_type
 */
function getTinyImgUrl($id) {
  $img = "../images/people_tiny/{$id}.jpg";
  if (is_file($img)) {
    $fname = "../images/people_tiny/{$id}.jpg";
    $count = 1;
    // Get the most recent file we have for this person.
    while (is_file($fname)) {
      $img = $fname;
      $fname = "../images/people_tiny/{$id}_{$count}.jpg";
      $count++;
    }
  } else {
    return "../images/tiny_person.jpg";
  }
  return $img;
}

function getPeopleList($startWith, $count) {
  $s = mysql_query("
    SELECT id, name, display_name
    FROM people
    LIMIT {$startWith}, {$count}
  ");
  $ret = array();
  while ($r = mysql_fetch_array($s)) {
    $r['tiny_image'] = getTinyImgUrl($r['id']);
    $ret[] = $r;

  }
  return $ret;
}


/**
 * This is hacked up utility used for finding links about people. It will
 * open a few iframes with a google search for their blog, and their website,
 * and it will allow me to add links that i get from those google searches.
 */

$id = (int)$_GET['id'];

// A person is selected. Fetch their name.
$person = new Person();
$person->setId($id);
$person->loadFromDb();

$COUNT = $_GET['count'] ? $_GET['count'] : 15;

$start = $_GET['start'] ? $_GET['start'] : 0;
$people = getPeopleList($start, $COUNT);

$t = new Smarty();
$t->assign('name', $person->displayName);
$t->assign('id', $person->id);
$t->assign('people', $people);
$t->assign('start', $start);
$t->assign('prev', $start - $COUNT);
$t->assign('next', $start + $COUNT);
$t->display('links_searches.tpl');

?>
