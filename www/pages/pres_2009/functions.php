<?php

/**
 * Returns an array of people given an SQL query. This is a private helper
 * method only for this file.
 * @param $sql The sql query.
 * @return Array
 * @private
 */
function getPresidentialCandidates($what, $year, $count) {
  $sql = mysql_query("
    SELECT
        p.display_name,
        parties.name AS party_name,
        candidates.idperson
    FROM pres_2009_people AS candidates
    LEFT JOIN people AS p ON p.id = candidates.idperson
    LEFT JOIN parties ON parties.id = candidates.idparty
    LEFT JOIN people_history AS h
      ON h.idperson = candidates.idperson AND what = 'catavencu/2008'
    WHERE candidates.retras = 0
    ORDER BY p.display_name LIMIT 0, $count");

  $people = array();
  while ($r = mysql_fetch_array($sql)) {
    $guy = $r;
    $guy['name'] = $r['display_name'];
    $guy['tiny_photo'] = getTinyImgUrl($r['idperson']);

    // TODO(vivi) In a different sql, get this guys history and stick it in an
    // array. Based on that, I should display stuff on the summary page.
    $people[] = $guy;
  }
  return $people;
}


/**
 * Returns the most recent videos with presidential candidates.
 * @param {number} $count The max number of videos to fetch.
 * @return Array The array, ready for the template.
 */
function getMostRecentVideos($count) {
  $s = mysql_query("
    SELECT v.idperson, v.thumb, v.title, v.player_url, v.time, 
        v.duration, v.watch_url
    FROM yt_videos AS v
    LEFT JOIN people_history AS h ON h.idperson = v.idperson
    WHERE h.what='pres/2009' AND v.approved = 1
    ORDER BY time DESC
    LIMIT 0, $count
    ");
  
  $res = array();
  while($r = mysql_fetch_array($s)) {
  	$r['title'] = stripslashes($r['title']);
    $res[] = $r;
  }

  return $res;
}

?>
