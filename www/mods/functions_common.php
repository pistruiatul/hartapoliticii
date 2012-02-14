<?php


/**
 * Returns the votes that are tagged with this specific tag id. If a person ID
 * is specified, the method also returns the person's individual position on
 * each of the votes.
 * @param $room
 * @param $year
 * @param $idtag
 * @param $personId
 * @return array
 */
function getVotesForTag($room, $year, $idtag, $personId=null) {
  if ($personId != null) {
    $sql = "
      SELECT votes.link, votes.description, tagged.inverse, votes.type,
          votes.time, position.vote
      FROM parl_tagged_votes AS tagged
      LEFT JOIN {$room}_{$year}_votes_details AS votes
        ON votes.link = tagged.link
      LEFT JOIN {$room}_{$year}_votes AS position
        ON position.link = tagged.link AND position.idperson = {$personId}
      WHERE
        tagged.idtag = {$idtag} AND
        tagged.votes_table = '{$room}_{$year}_votes_details'
    ";
  } else {
    $sql = "
      SELECT votes.link, votes.description, tagged.inverse, votes.type,
          votes.time
      FROM parl_tagged_votes AS tagged
      LEFT JOIN {$room}_{$year}_votes_details AS votes
        ON votes.link = tagged.link
      WHERE
        tagged.idtag = {$idtag} AND
        tagged.votes_table = '{$room}_{$year}_votes_details'
    ";
  }

  $votes = array();

  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    $r['time'] = $r['time'] / 1000;
    $votes[] = $r;
  }
  return $votes;
}


function getTagNameForId($id) {
  $s = mysql_query("
    SELECT tag FROM parl_tags WHERE id = $id");

  if ($r = mysql_fetch_array($s)) return $r['tag'];
  return '';
}


function getTagDescriptionForId($id) {
  $s = mysql_query("
    SELECT description FROM parl_tags WHERE id = {$id}");

  if ($r = mysql_fetch_array($s)) return $r['description'];
  return '';
}


function getTagsList($table, $uid) {
  $s = mysql_query("
    SELECT tags.tag, count(*) AS num, tags.id, tagged.uid
    FROM parl_tagged_votes AS tagged
    LEFT JOIN parl_tags AS tags ON tags.id = tagged.idtag
    WHERE
      tagged.votes_table = '{$table}' AND
      tagged.uid = {$uid}
    GROUP BY idtag
  ");
  $tags = array();
  while ($r = mysql_fetch_array($s)) {
    $csum = md5($table . $uid . $r['id'] . 'tagsekret');
    $r['csum'] = $csum;

    $tags[] = $r;
  }
  return $tags;
}


function getTaggedVoteCount($idperson, $room, $year, $vote, $idtag, $uid) {
  $baseQuery = "
    SELECT *
    FROM parl_tagged_votes AS tagged
    LEFT JOIN {$room}_{$year}_votes AS votes
      ON votes.link = tagged.link
    WHERE
      tagged.idtag = {$idtag} AND
      votes.idperson = {$idperson}";

  if ($vote == 'DA' || $vote == 'NU') {
    $s = mysql_query($baseQuery . " AND votes.vote='{$vote}' ".
                     "AND tagged.inverse=0");
    $plainCount = mysql_num_rows($s);

    $revvote = $vote == 'DA' ? 'NU' : 'DA';
    $s = mysql_query($baseQuery . " AND votes.vote='{$revvote}' ".
                     "AND tagged.inverse=1");
    $revCount = mysql_num_rows($s);

    return $plainCount + $revCount;
  } else {
    $s = mysql_query($baseQuery . " AND votes.vote='{$vote}'");
    return mysql_num_rows($s);
  }
}


function getBeliefContext($room, $year, $uid, $idperson, $idtag, $possible) {
  // Get the list of votes tagged like that.
  // Crossed with my senator's votes (da, nu, ab, mi) + count for each.
  $danum = getTaggedVoteCount($idperson, $room, $year, 'DA', $idtag, $uid);
  $nunum = getTaggedVoteCount($idperson, $room, $year, 'NU', $idtag, $uid);
  $abnum = getTaggedVoteCount($idperson, $room, $year, 'Ab', $idtag, $uid);
  $minum = getTaggedVoteCount($idperson, $room, $year, '-', $idtag, $uid);

  $total = $possible;

  $sum = $danum + $nunum + $abnum + $minum;

  $width = 220;

  $vpx = $width / $total;

  // The red area.
  $w2 = $vpx * $nunum;
  // the gray area in the middle;
  $w3 = $vpx * ($abnum + $minum + ($total - $sum));
  // the grayed out area on the left
  $w1 = $width - $w2 - $w3 / 2;
  // the green area on the right.
  $w4 = $vpx * $danum;
  // the grayed out area on the right;
  $w5 = $width - $w4 - $w3 / 2;

  $c = array();
  $c['w1'] = $w1;
  $c['w2'] = $w2;
  $c['w3'] = $w3;
  $c['w4'] = $w4;
  $c['w5'] = $w5;

  $c['c2'] = $nunum;
  $c['c3'] = $total - $sum;
  $c['c4'] = $danum;
  $c['c5'] = $minum + $abnum;

  return $c;
}


function showBeliefs($room, $year, $uid, $idperson) {
  if ($uid == 0) {
    return;
  }
  // Get the tags that are for my table (this gets both the count and
  // the tags themselves).
  $tags = getTagsList("{$room}_{$year}_votes_details", $uid);

  $t = new Smarty();
  $t->display("parl_person_beliefs_header.tpl");

  foreach ($tags as $tag) {
    $c = getBeliefContext($room, $year, $uid, $idperson, $tag['id'],
                          $tag['num']);

    $t = new Smarty();
    $t->assign('tag', $tag['tag']);

    $t->assign('w1', $c['w1']);
    $t->assign('w2', $c['w2']);
    $t->assign('w3', $c['w3']);
    $t->assign('w4', $c['w4']);
    $t->assign('w5', $c['w5']);

    $t->assign('c2', $c['c2']);
    $t->assign('c3', $c['c3']);
    $t->assign('c4', $c['c4']);
    $t->assign('c5', $c['c5']);

    $link =
        "?cid=15&tagid={$tag['id']}&room={$room}&u={$uid}&csum={$tag['csum']}";
    $t->assign('taglink', $link);

    $t->display('parl_person_belief.tpl');
  }
}

?>
