<?php
/**
 * Created by IntelliJ IDEA.
 * User: vivi
 * Date: 11/19/16
 * Time: 6:27 PM
 */


include_once('pages/functions_common.php');
include_once('hp-includes/string_utils.php');

$seats = array(
    'Alba' => [2, 5],
    'Arad' => [3, 7],
    'Arges' => [4, 9],
    'Bacau' => [4, 10],
    'Bihor' => [4, 9],
    'Bistrita Nasaud' => [2, 5],
    'Botosani' => [3, 6],
    'Brasov' => [4, 9],
    'Braila' => [2, 5],
    'Buzau' => [3, 7],
    'Caras Severin' => [2, 5],
    'Calarasi' => [2, 4],
    'Cluj' => [4, 10],
    'Constanta' => [5, 11],
    'Covasna' => [2, 4],
    'Dambovita' => [3, 7],
    'Dolj' => [4, 10],
    'Galati' => [4, 9],
    'Giurgiu' => [2, 4],
    'Gorj' => [2, 5],
    'Harghita' => [2, 5],
    'Hunedoara' => [3, 6],
    'Ialomita' => [2, 4],
    'Iasi' => [5, 12],
    'Ilfov' => [2, 5],
    'Maramures' => [3, 7],
    'Mehedinti' => [2, 4],
    'Mures' => [4, 8],
    'Neamt' => [3, 8],
    'Olt' => [3, 6],
    'Prahova' => [5, 11],
    'Satu Mare' => [2, 5],
    'Salaj' => [2, 4],
    'Sibiu' => [3, 6],
    'Suceava' => [4, 10],
    'Teleorman' => [2, 5],
    'Timis' => [4, 10],
    'Tulcea' => [2, 4],
    'Vaslui' => [3, 7],
    'Valcea' => [2, 6],
    'Vrancea' => [2, 5],
    'Bucuresti' => [13, 29],
);

function getPartiesOnCountyList($county) {
  $sql =
      "SELECT count(*) as num_cand, parties.id, parties.name AS party, parties.minoritati " .
      "FROM results_2016 AS results ".
      "LEFT JOIN parties ON parties.id = results.idpartid ".
      "WHERE colegiu = '{$county}' " .
      "GROUP BY parties.id " .
      "ORDER BY parties.minoritati ASC, num_cand DESC";

  $s = mysql_query($sql);

  $parties = array();

  while ($r = mysql_fetch_array($s)) {
    $p = new Party($r['id']);
    $p->lists[$county] = $p->get2016List($county);
    $parties[] = $p;

  }
  return $parties;

}

function getPeopleIdsInCountyLists($county) {
  $sql = "SELECT idperson FROM results_2016 WHERE colegiu = '{$county}' ORDER BY idcandidat ASC";
  $s = mysql_query($sql);

  $candidates = array();

  while ($r = mysql_fetch_array($s)) {
    $candidates[] = $r['idperson'];
  }
  return $candidates;
}

function getPercentageFor($party) {
  $key = strtolower($party->name);
  if ($_GET[$key]) {
    return floatval($_GET[$key]);
  }
  return 0;
}

function sortPercentages($a, $b) {
  return $a['p'] < $b['p'];
}

function getPartyPercentagesFromUrl($parties) {
  $percentages = array();
  foreach ($parties as $party) {
    $foo = array();
    $foo['name'] = strtolower($party->name);
    $foo['p'] = getPercentageFor($party);

    $percentages[] = $foo;
  }
  usort($percentages, "sortPercentages");
  return $percentages;
}

function getElectedCountForParty($num_seats, $party, $parties) {
  // Then add up the percentages from all the parties for the URL
  $percentages = getPartyPercentagesFromUrl($parties);
  $sum = 0;
  foreach ($percentages as $p) {
    $sum += $p['p'];
  }

  // Then divide the percentage for this one party by the sum, and round down to seats.
  $my_percentage = getPercentageFor($party);

  return floor($num_seats * $my_percentage / $sum);
}

function mark2016Candidates($num_seats, $college_name, $parties) {
  foreach ($parties as $party) {
    $count = getElectedCountForParty($num_seats, $party, $parties);
    $party->sortIndex = $count;
    for ($i = 0; $i < count($party->lists[$college_name]); $i++) {
      $party->lists[$college_name][$i]['makes_it'] = $i < $count;
    }
  }
}

?>