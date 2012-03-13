<?php
/**
 * Returns all the possible final votes from this legislature.
 * @param $room The room.
 * @param $year The year.
 * @return {int} The number of votes.
 */
function getAllFinalVotes($room, $year, $partyId) {
  $s = mysql_query("
    SELECT value FROM parties_facts
    WHERE attribute='{$room}{$year}/all_votes' AND idparty={$partyId}
  ");
  if ($r = mysql_fetch_array($s)) {
    return $r['value'];
  }
  return -1;
}

/**
 * Get all the final votes in which a certain party participated with more than
 * four people.
 * @param $room The room.
 * @param $year The year.
 * @param $partyId The id of the party.
 * @return {int} The number of votes.
 */
function getPartyFinalVotes($room, $year, $partyId) {
  $s = mysql_query("
    SELECT value
    FROM parties_facts
    WHERE attribute='{$room}{$year}/party_votes' AND idparty={$partyId}
  ");
  if ($r = mysql_fetch_array($s)) {
    return $r['value'];
  }
  return -1;
}

/**
 * Get all the final votes in which a certain party participated with more than
 * four people.
 * @param $room The room.
 * @param $year The year.
 * @param $partyId The id of the party.
 * @return {int} The number of votes.
 */
function getPartyLineFinalVotes($room, $year, $partyId) {
  $s = mysql_query("
    SELECT value
    FROM parties_facts
    WHERE attribute='{$room}{$year}/party_line_votes' AND idparty={$partyId}
  ");
  if ($r = mysql_fetch_array($s)) {
    return $r['value'];
  }
  return -1;
}

?>
