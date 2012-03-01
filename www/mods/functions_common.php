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


function getTagAuthorUid($id) {
  $s = mysql_query("
    SELECT uid FROM parl_tags WHERE id = $id");

  if ($r = mysql_fetch_array($s)) return $r['uid'];
  return '';
}


function getTagDescriptionForId($id) {
  $s = mysql_query("
    SELECT description FROM parl_tags WHERE id = {$id}");

  if ($r = mysql_fetch_array($s)) return $r['description'];
  return '';
}


function getTagsList($table, $uid) {
  if (!$uid) {
    // Get the public tags.
    $s = mysql_query("
      SELECT tags.tag, count(*) AS num, tags.id, tagged.uid, tags.description
      FROM parl_tagged_votes AS tagged
      LEFT JOIN parl_tags AS tags ON tags.id = tagged.idtag
      WHERE
        tagged.votes_table = '{$table}' AND
        tags.public = 1
      GROUP BY idtag
    ");

  } else {
    $s = mysql_query("
      SELECT tags.tag, count(*) AS num, tags.id, tagged.uid
      FROM parl_tagged_votes AS tagged
      LEFT JOIN parl_tags AS tags ON tags.id = tagged.idtag
      WHERE
        tagged.votes_table = '{$table}' AND
        tagged.uid = {$uid}
      GROUP BY idtag
    ");
  }

  $tags = array();
  while ($r = mysql_fetch_array($s)) {
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
  $yes_cnt = getTaggedVoteCount($idperson, $room, $year, 'DA', $idtag, $uid);
  $no_cnt = getTaggedVoteCount($idperson, $room, $year, 'NU', $idtag, $uid);
  $abs_cnt = getTaggedVoteCount($idperson, $room, $year, 'Ab', $idtag, $uid);
  $missing_cnt = getTaggedVoteCount($idperson, $room, $year, '-', $idtag, $uid);

  $records = $yes_cnt + $no_cnt + $abs_cnt + $missing_cnt;
  $missing_cnt += $possible - $records;

  // This is the width of HALF of the area for votes. This means that we have
  // an area that allows for $possible negative votes and also $possible
  // positive votes at the same time.
  $width = 200;

  // How many pixels we have per each vote?
  $px_per_vote = $width / $possible;

  // The red area.
  $red_px = $px_per_vote * $no_cnt;
  // the grayed out area on the left
  $gray_px_left = $width - $red_px;

  // the green area on the right.
  $green_px = $px_per_vote * $yes_cnt;
  // the grayed out area on the right;
  $gray_px_right = $width - $green_px;

  $c = array();
  $c['gray_px_left'] = $gray_px_left;
  $c['red_px'] = $red_px;
  $c['green_px'] = $green_px;
  $c['gray_px_right'] = $gray_px_right;

  $c['no_cnt'] = $no_cnt;
  $c['yes_cnt'] = $yes_cnt;
  $c['abs_cnt'] = $abs_cnt;
  $c['missing_cnt'] = $missing_cnt;

  return $c;
}


function showBeliefs($room, $year, $uid, $idperson) {
  // Get the tags that are for my table (this gets both the count and
  // the tags themselves).
  $tags = getTagsList("{$room}_{$year}_votes_details", false);

  if (sizeof($tags) > 0) {
    $t = new Smarty();
    $t->assign('title', 'Poziția pe următoarele issue-uri');
    $t->display("parl_person_beliefs_header.tpl");
    foreach ($tags as $tag) {
      displayOneIndividualTag($room, $year, $uid, $idperson, $tag);
    }
  }

  if ($uid != 0) {
    $your_tags = getTagsList("{$room}_{$year}_votes_details", $uid);

    if (sizeof($your_tags) > 0) {
      $t = new Smarty();
      $t->assign('title', 'Poziția pe tag-urile tale private');
      $t->display("parl_person_beliefs_header.tpl");
      foreach ($your_tags as $tag) {
        displayOneIndividualTag($room, $year, $uid, $idperson, $tag);
      }
    }
  }
}


function displayOneIndividualTag($room, $year, $uid, $idperson, $tag) {
  $c = getBeliefContext($room, $year, $uid, $idperson, $tag['id'],
                          $tag['num']);

  $t = new Smarty();
  $t->assign('tag', $tag['tag']);

  $t->assign('w1', $c['w1']);
  $t->assign('w2', $c['w2']);
  $t->assign('w3', $c['w3']);
  $t->assign('w4', $c['w4']);
  $t->assign('w5', $c['w5']);

  // TODO(vivi): comment this more, right now this is ridiculous.
  $t->assign('c2', $c['c2']);
  $t->assign('c3', $c['c3']);
  $t->assign('c4', $c['c4']);
  $t->assign('c5', $c['c5']);

  $link = "?cid=15&tagid={$tag['id']}&room={$room}";
  $t->assign('taglink', $link);
  $t->assign('description', $tag['description']);

  $t->assign('room', $room);
  $t->assign('year', $year);
  $t->assign('person_id', $idperson);
  $t->assign('tagid', $tag['id']);

  $t->display('parl_person_belief.tpl');
}

?>
