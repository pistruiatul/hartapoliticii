<?php
include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');
include_once('pages/functions_common.php');

// Just get me a list with the people that are in cdep/2008
function getPeopleList($room, $year) {
  $s = mysql_query("
    SELECT people.id, display_name, p.name as party_name
    FROM people
    LEFT JOIN people_history AS h ON h.idperson = people.id
    LEFT JOIN people_facts AS f ON f.idperson = people.id
    LEFT JOIN parties AS p ON f.value = p.id
    WHERE h.what = '{$room}/{$year}' AND
          f.attribute = 'party'
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

if (!$_GET['iframe']) {
  include('header.php');
}

if ($csum == md5($room . '_' . $year . '_votes_details' . $uid . $tagid .
                 'tagsekret')) {
  $t = new Smarty();

  $t->assign('tag', getTagNameForId($tagid));
  $t->assign('description', getTagDescriptionForId($tagid));

  $votes = getVotesForTag($room, $year, $tagid, $uid);
  $possible = sizeof($votes);
  $t->assign('votes', $votes);

  $people = getPeopleList($room, $year);

  $non_zero_people = array();
  $zero_people = array();

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
    $people[$i]['c5'] = $c['c5'];

    // Since I'm here, fix the name. A little hacky.
    $people[$i]['link'] = "?cid=9&id=" . $people[$i]['id'];
    $people[$i]['tiny_photo'] = getTinyImgUrl($people[$i]['id']);

    if ($c['c2'] + $c['c4']) {
      array_push($non_zero_people, $people[$i]);
    } else {
      array_push($zero_people, $people[$i]);
    }
  }

  usort($non_zero_people, "beliefCmp");

  $t->assign('people', $non_zero_people);
  $t->assign('absentees', $zero_people);

  $t->assign('room', $room);
  $t->assign('year', $year);
  $t->assign('tagid', $tagid);

  $t->assign('user_login', getUserLogin($uid));

  $t->display('compass_show_tag.tpl');

} else {
  echo "Wrong checksum";
}

?>
