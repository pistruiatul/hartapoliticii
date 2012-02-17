<?
include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');
require_once('../wp-config.php');

$uid = is_user_logged_in() ? $current_user->ID : 0;

/**
 * Return the id of the tag. If the tag does not exist, insert it.
 * @param $tag
 * @return unknown_type
 */
function getTagId($tag, $uid) {
  $tag = mysql_real_escape_string($tag);

  $s = mysql_query("SELECT id FROM parl_tags WHERE tag='{$tag}' AND uid={$uid}");
  if ($r = mysql_fetch_array($s)) {
    // Update the max time this person has been here.
    return $r['id'];
  } else {
    // Okay, insert the tag now.
    $si = mysql_query("INSERT INTO parl_tags(tag, uid) VALUES('{$tag}', {$uid})");
    return mysql_insert_id();
  }
}


/**
 * Returns the link identifying a vote, for a room and year. We use the link
 * because it's a stable identifier, whereas the id mayb change over time as
 * we update our tables. The idvote is only used temporarily for adding and
 * removing tags, since it's an atomic operation relative to updating all votes.
 * @param {string} $room The room, like 'senat' or 'cdep'.
 * @param {string} $year What year this is. For now, only 2008.
 * @param {int} $idvote The id in the senat_2008_votes_details table.
 * @return {string} The link.
 */
function getVoteLink($room, $year, $idvote) {
  $s = mysql_query("
    SELECT link
    FROM {$room}_{$year}_votes_details
    WHERE id='{$idvote}'
  ");

  if ($r = mysql_fetch_array($s)) {
    return $r['link'];
  }
  return '';
}


/**
 * Adds a tag to an existing vote. The tag is something like 'reforma medicală'
 * or 'Adrian Năstase'.
 * @param $room
 * @param $year
 * @param $idvote
 * @param $tag
 * @return unknown_type
 */
function addVoteTag($room, $year, $idvote, $tag, $inverse) {
  global $uid;
  if ($uid == 0) {
    return;
  }

  // get the tag id;
  $idtag = getTagId($tag, $uid);
  $link = getVoteLink($room, $year, $idvote);

  $s = mysql_query("
    SELECT id
    FROM parl_tagged_votes
    WHERE
      votes_table = '{$room}_{$year}_votes_details' AND
      link = '{$link}' AND
      uid = {$uid} AND
      idtag = {$idtag}
  ");
  echo $link;

  if ($r = mysql_fetch_array($s)) {
    return;
  } else {
    $si = mysql_query("
      INSERT INTO
          parl_tagged_votes(votes_table, idvote, link, idtag, uid, inverse)
      VALUES('{$room}_{$year}_votes_details', {$idvote}, '{$link}', {$idtag},
          {$uid}, {$inverse})
    ");
    return mysql_insert_id();
  }
}


/**
 * Removes a tag from a certain vote. It first makes sure that it matches and
 * it's the right tag to remove, so we don't end up removing bogus tags.
 */
function removeVoteTag($room, $year, $idvote, $tag) {
  global $uid;
  if ($uid == 0) {
    return;
  }

  // get the tag id;
  $idtag = getTagId($tag, $uid);

  $s = mysql_query("
    SELECT id
    FROM parl_tagged_votes
    WHERE
      votes_table = '{$room}_{$year}_votes_details' AND
      idvote = {$idvote} AND
      uid = {$uid} AND
      idtag = {$idtag}
  ");
  if ($r = mysql_fetch_array($s)) {
    $si = mysql_query("
      DELETE FROM parl_tagged_votes
      WHERE
        votes_table = '{$room}_{$year}_votes_details' AND
        idvote = {$idvote} AND
        uid = {$uid} AND
        idtag = {$idtag}
    ");
    echo 'done';
  } else {
    echo 'error';
  }
}

// ----------- This is the main area where stuff happens.

$delete = trim($_GET['delete']);

$room = trim($_GET['room']);
if ($room != 'senat' && $room != 'cdep') {
  die('get a room!');
}

$year = (int)trim($_GET['year']);
$idvote = (int)trim($_GET['idvote']);
$tag = trim($_GET['tag']);

$inverse = $_GET['inverse'] ? 1 : 0;

if ($room && $year && $idvote && $tag) {
  if (!$delete) {
    $id = addVoteTag($room, $year, $idvote, $tag, $inverse);
    echo $id;
  } else {
    removeVoteTag($room, $year, $idvote, $tag);
  }
}
?>
