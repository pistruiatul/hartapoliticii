<?php

/**
 * Returns the name of the person holding the prime-minister function at the
 * time passed in as a parameter.
 * @param {Number} $time The time we are interested in.
 * @return {String} The name of the prime minister.
 */
function getPrimeMinister($time) {
  $sql = "
    SELECT g.title, p.display_name, p.id
    FROM govro_people AS g
    LEFT JOIN people AS p ON p.id = g.idperson
    WHERE title='Prim-ministru' AND
        mintime <= {$time} AND
        (maxtime >= {$time} OR maxtime = 0)";

  $s = mysql_query($sql);
  $r = mysql_fetch_array($s);
  return array(
    'display_name' => $r['display_name'],
    'person_id' => $r['id']);
}


$s = mysql_query("
    SELECT *
    FROM govro_people
    WHERE idperson = {$person->id}
    ORDER BY mintime DESC");


$positions = array();
while ($r = mysql_fetch_array($s)) {
  // For each entry where this person held a position in the government, figure
  // out who was the prime minister.
  //
  // NOTE(vivi): We currently assume that each entry for this person happens
  // only under one government (one prime minister) so we only have to check
  // the start time.
  // TODO(vivi): Revisit this if we change the way we store stuff.

  $prime_minister = getPrimeMinister($r['mintime']);
  $position = array(
    'title' => $r['title'],
    'cabinet' => $prime_minister,
    'link' => $r['link'],
    'time_from' => $r['mintime'],
    'time_to' => $r['maxtime']
  );
  array_push($positions, $position);
}

$t = new Smarty();
$t->assign('person_name', $person->getUrlName());
$t->assign('positions', $positions);
$t->display('mod_gov_ro.tpl');

?>
