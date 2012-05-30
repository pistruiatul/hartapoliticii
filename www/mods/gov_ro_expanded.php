<?php

include('pages/functions_common.php');

/**
 * Returns the name of the person holding the prime-minister function at the
 * time passed in as a parameter.
 * @param {Number} $time The time we are interested in.
 * @return {String} The name of the prime minister.
 */
function getPrimeMinister($time) {
  $sql = "
    SELECT g.idperson, g.title, g.mintime, g.maxtime, p.display_name
    FROM govro_people AS g
    LEFT JOIN people AS p ON p.id = g.idperson
    WHERE title='Prim-ministru' AND
        mintime <= {$time} AND
        (maxtime >= {$time} OR maxtime = 0)";

  $s = mysql_query($sql);
  $r = mysql_fetch_array($s);

  return array(
    'title' => $r['title'],
    'display_name' => $r['display_name'],
    'time_from' => $r['mintime'],
    'time_to' => $r['maxtime'],
    'idperson' => $r['idperson'],
    'tiny_img_url' => getTinyImgUrl($r['idperson'])
  );
}

function getVicePrimeMinister($prime_minister) {
  $sql = "
    SELECT g.idperson, g.title, g.mintime, g.maxtime, p.display_name
    FROM govro_people AS g
    LEFT JOIN people AS p ON p.id = g.idperson
    WHERE title LIKE 'viceprim%' AND
        mintime >= {$prime_minister[time_from]} AND
        ((maxtime = 0 AND {$prime_minister[time_to]} = 0) OR
        (maxtime != 0 AND maxtime <= {$prime_minister[time_to]}))
    ORDER BY title";

  $s = mysql_query($sql);
  $r = mysql_fetch_array($s);
  $vice = array (
      'display_name' => $r['display_name'],
      'title' => $r['title'],
      'idperson' => $r['idperson'],
      'time_from' => $r['mintime'],
      'time_to' => $r['maxtime'],
      'tiny_img_url' => getTinyImgUrl($r['idperson'])
    );

  return $vice;
}

function getGovernment($prime_minister, $current_person_id) {
  $sql = "
    SELECT g.idperson, g.title, g.mintime, g.maxtime, p.display_name
    FROM govro_people AS g
    LEFT JOIN people AS p ON p.id = g.idperson
    WHERE g.idperson != {$current_person_id} AND
        title != 'Prim-ministru' AND
        title NOT LIKE 'viceprim%' AND
        mintime >= {$prime_minister[time_from]} AND
        ((maxtime = 0 AND {$prime_minister[time_to]} = 0) OR
        (maxtime != 0 AND maxtime <= {$prime_minister[time_to]}))
    ORDER BY title";

  $government = array();

  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    $member = array (
      'display_name' => $r['display_name'],
      'title' => $r['title'],
      'idperson' => $r['idperson'],
      'time_from' => $r['mintime'],
      'time_to' => $r['maxtime'],
      'tiny_img_url' => getTinyImgUrl($r['idperson'])
    );

    array_push($government, $member);
  }

  return $government;
}

$s = mysql_query("
    SELECT *
    FROM govro_people
    WHERE idperson = {$person->id}
    ORDER BY mintime DESC");

$prime_ministers = array();
$viceprime_ministers = array();
$positions = array();
$governments = array();
while ($r = mysql_fetch_array($s)) {
  // For each entry where this person held a position in the government, figure
  // out who was the prime minister.
  //
  // NOTE(vivi): We currently assume that each entry for this person happens
  // only under one government (one prime minister) so we only have to check
  // the start time.
  // TODO(vivi): Revisit this if we change the way we store stuff.

  $prime_minister = getPrimeMinister($r['mintime']);
  $viceprime_minister = getVicePrimeMinister($prime_minister);

  $position = array(
    'display_name' => $person->displayName,
    'title' => $r['title'],
    'idperson' => $person->id,
    'link' => $r['link'],
    'time_from' => $r['mintime'],
    'time_to' => $r['maxtime'],
    'tiny_img_url' => getTinyImgUrl($person->id)
  );

  $government = getGovernment($prime_minister, $person->id);

  array_push($prime_ministers, $prime_minister);
  array_push($viceprime_ministers, $viceprime_minister);
  array_push($positions, $position);
  array_push($governments, $government);
}

$t = new Smarty();
$t->assign('prime_ministers', $prime_ministers);
$t->assign('viceprime_ministers', $viceprime_ministers);
$t->assign('positions', $positions);
$t->assign('governments', $governments);
$t->display('mod_gov_ro_expanded.tpl');

?>
