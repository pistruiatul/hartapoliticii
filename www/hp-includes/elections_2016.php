<?php
/**
 * Created by IntelliJ IDEA.
 * User: vivi
 * Date: 11/19/16
 * Time: 6:27 PM
 */


include_once('pages/functions_common.php');
include_once('hp-includes/string_utils.php');


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


?>