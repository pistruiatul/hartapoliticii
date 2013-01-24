<?php
include_once('hp-includes/person_class.php');
include_once('hp-includes/people_util.php');

include_once('mods/functions_common.php');
include_once('pages/functions_common.php');


/**
 *  Just get me a list with the people that are in cdep/2008
 */
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


/**
 * A comparator for two people so that we sort them according to the score
 * of whom overall voted more positive than negative on the issue.
 * @param $a
 * @param $b
 * @return bool
 */
function beliefCmp($a, $b) {
  $v1 = $a['yes_cnt'] - $a['no_cnt'];
  $v2 = $b['yes_cnt'] - $b['no_cnt'];

  if ($v1 == $v2) {
    if ($a['yes_cnt'] == $b['yes_cnt']) {
      return $a['no_cnt'] > $b['no_cnt'];
    }
    return $a['yes_cnt'] < $b['yes_cnt'];
  }
  return $v1 < $v2;
}


$tagid = (int)$_GET['tagid'];
$room = $_GET['room'] == 'cdep' ? 'cdep' : 'senat';
$csum = $_GET['csum'];

// HACK?
$year = '2008';
$title = 'Tag "' . getTagNameForId($tagid) . '"';

if (!$_GET['iframe']) include('header.php');

$t = new Smarty();
$t->caching = 2;
$t->cache_lifetime = 86400;

if (!$t->is_cached('compass_show_tag.tpl', $tagid)) {

  $t->assign('tag', getTagNameForId($tagid));
  $t->assign('description', getTagDescriptionForId($tagid));

  $authorUid = getTagAuthorUid($tagid);

  $votes = getVotesForTag($room, $year, $tagid, $authorUid);
  $possible = sizeof($votes);
  $t->assign('votes', $votes);

  $people = getPeopleList($room, $year);

  $non_zero_people = array();
  $zero_people = array();

  for ($i = 0; $i < sizeof($people); $i++) {
    $context = getBeliefContext($room, $year, $authorUid, $people[$i]['id'], $tagid,
                                $possible, 200);

    foreach ($context as $key => $value) {
      $people[$i][$key] = $value;
    }

    // Since I'm here, fix a few things. A little hacky.
    $people[$i]['link'] = "?cid=9&id=" . $people[$i]['id'];
    $people[$i]['tiny_photo'] = getTinyImgUrl($people[$i]['id']);

    if ($context['yes_cnt'] + $context['no_cnt'] > 0) {
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

  $t->assign('user_login', getUserLogin($authorUid));
}
$t->display('compass_show_tag.tpl', $tagid);

?>
