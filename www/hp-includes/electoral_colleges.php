<?php

include_once('pages/functions_common.php');

/**
 * Returns the id of the winner of the 2008 elections.
 *
 * TODO(vivi): Refactor and generalize this.
 *
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 */
function getWinner2008ForCollege($college) {
  $sql =
    "SELECT college, idperson_winner, idperson_runnerup ".
    "FROM results_2008_agg ".
    "WHERE college = '{$college}'";

  $r = mysql_fetch_array(mysql_query($sql));
  return $r['idperson_winner'];
}


/**
 * Returns the list of candidates and their results for this college. For now
 * this method is not generic at all, hence the very specific name.
 *
 * TODO(vivi): Refactor and generalize this.
 *
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 */
function getResults2008ForCollege($college) {
  $sql =
    "SELECT people.id, people.display_name, people.name, res.voturi, ".
        "parties.name AS party, cand.reason, cand.difference, ".
        "parties.minoritati ".
    "FROM results_2008 AS res ".
    "LEFT JOIN people ON people.id = res.idperson ".
    "LEFT JOIN parties ".
        "ON parties.id = res.idpartid ".
    "LEFT JOIN results_2008_candidates AS cand ".
        "ON cand.idperson = people.id ".
    "WHERE colegiu = '{$college}' ".
    "ORDER BY voturi DESC";

  $s = mysql_query($sql);

  $candidates = array();

  while ($r = mysql_fetch_array($s)) {
    $person_object = $r;
    $person_object['tiny_img_url'] = getTinyImgUrl($r['id']);
    $candidates[] = $person_object;
  }
  return $candidates;
}


/**
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 * @return
 */
function getDescription2008ForCollege($college) {
  $parts = explode(" ", $college);

  $sql =
    "SELECT description ".
    "FROM alegeri_2008_colleges ".
    "WHERE url LIKE '%{$parts[1]}-{$parts[0]}'";

  $r = mysql_fetch_array(mysql_query($sql));
  return $r['description'];
}

?>