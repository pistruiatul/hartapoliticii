<?php
include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');


// Just get me a list with the people that are in cdep/2008
function getPeopleList($room, $year) {
  $s = mysql_query("
    SELECT people.id, display_name
    FROM people
    LEFT JOIN people_history AS h ON h.idperson = people.id
    WHERE h.what = '{$room}/{$year}'
  ");

  $list = array();
  while ($r = mysql_fetch_array($s)) {
    $list[] = $r;
  }
  return $list;
}

function beliefCmp($a, $b) {
  $v1 = $a['c4'] - $a['c2'];
  $v2 = $b['c4'] - $b['c2'];
  if ($v1 == $v2) {
    if ($a['c4'] == $b['c4']) {
      return $a['c2'] > $b['c2'];
    }
    return $a['c4'] < $b['c4'];
  }
  return $v1 < $v2;
}


$tagid = (int)$_GET['tagid'];
$room = $_GET['room'] == 'cdep' ? 'cdep' : 'senat';
$uid = (int)$_GET['u'];
$csum = $_GET['csum'];

// HACK?
$year = '2008';

$title = 'Tag "' . getTagNameForId($tagid) . '"';
$nowarning = true;
include('header.php');

if ($csum == md5($room . '_' . $year . '_votes_details' . $uid . $tagid .
                 'tagsekret')) {
  $t = new Smarty();

  $t->assign('tag', getTagNameForId($tagid));
  $votes = getVotesForTag($room, $year, $tagid, $uid);
  $possible = sizeof($votes);
  $t->assign('votes', $votes);

  $people = getPeopleList($room, $year);
  for ($i = 0; $i < sizeof($people); $i++) {
    $c = getBeliefContext($room, $year, $uid, $people[$i]['id'], $tagid,
                          $possible);
    $people[$i]['w1'] = $c['w1'];
    $people[$i]['w2'] = $c['w2'];
    $people[$i]['w3'] = $c['w3'];
    $people[$i]['w4'] = $c['w4'];
    $people[$i]['w5'] = $c['w5'];
    $people[$i]['c2'] = $c['c2'];
    $people[$i]['c3'] = $c['c3'];
    $people[$i]['c4'] = $c['c4'];

    // Since I'm here, fix the name. A little hacky.
    $people[$i]['link'] = "?cid=9&id=" . $people[$i]['id'];
  }

  usort($people, "beliefCmp");
  $t->assign('people', $people);

  $t->assign('user_login', getUserLogin($uid));
  $t->display('compass_show_tag.tpl');
}

?>
