<?php
include_once('hp-includes/string_utils.php');
include_once('wp-includes/formatting.php');

/**
 * Returns an array of people given an SQL query. This is a private helper
 * method only for this file.
 * @param $sql The sql query.
 * @return Array
 * @private
 */
function getMostPresentInNews($count, $what = NULL, $tstart = NULL,
                              $tend = NULL, $for_ids = NULL,
                              $source = NULL) {
  if ($tstart == NULL && $tend == NULL) {
    $tend = time() + 60 * 60 * 8; // Adjusting for the time zone.
    $tstart = time() - 60 * 60 * 24 * 7;
  }

  $what_filter = "";
  $history_join = "";
  if ($what != NULL) {
    $history_join = "LEFT JOIN people_history AS h
                     ON h.idperson = p.idperson";
    $what_filter = "AND h.what = '{$what}'";
  }

  $restrict_to_ids = "";
  if ($for_ids != NULL) {
    $restrict_to_ids = "AND p.idperson IN (" . implode(",", $for_ids) . ")";
  }

  $where_source = '';
  if ($source) {
    $where_source = "AND a.source LIKE '${source}'";
  }

  $query = "
    SELECT
      count(*) AS mentions, p.idperson, people.display_name, people.name
    FROM news_people AS p
    LEFT JOIN people ON p.idperson = people.id
    LEFT JOIN news_articles AS a ON a.id = p.idarticle
    {$history_join}
    WHERE a.time > {$tstart} AND
          a.time < {$tend} AND
          a.source != 'ugc'
          {$where_source}
          {$restrict_to_ids}
          {$what_filter}
    GROUP BY p.idperson
    ORDER BY mentions DESC, p.idperson
    LIMIT 0, $count";

  $sql = mysql_query($query);

  $people = array();
  while ($r = mysql_fetch_array($sql)) {
    $guy = $r;

    $guy['name'] = str_replace(' ', '+', $r['name']);
    $guy['tiny_photo'] = getTinyImgUrl($r['idperson']);

    // TODO(vivi) In a different sql, get this guys history and stick it in an
    // array. Based on that, I should display stuff on the summary page.
    $people[] = $guy;
  }
  return $people;
}


/**
 * Given a list of people produced by getMostPresentInNews, also add the
 * information about where they were last week.
 * @param $list
 * @return Array The list with the modified stuff.
 */
function newsAddPreviousWeekToList($list, $what = NULL, $source = 'mediafax') {
  // first, extract the list of id's.
  $ids = array();
  for ($i = 0; $i < sizeof($list); $i++) {
    $ids[] = $list[$i]['idperson'];
  }

  // find out what was last Sunday.
  $tend = time() - 60 * 60 * 24 * 7 + 60 * 60 * 8; // Adjusting for the timezone
  $tstart = $tend - 60 * 60 * 24 * 7;

  $newlist = getMostPresentInNews(100, $what, $tstart, $tend, $ids, $source);
  $hash = array();
  for ($i = 0; $i < sizeof($newlist); $i++) {
    $hash['k' . $newlist[$i]['idperson']] = $newlist[$i]['mentions'];
  }

  for ($i = 0; $i < sizeof($list); $i++) {
    $list[$i]['mentions_prev'] = $hash['k' . $list[$i]['idperson']] ?
        $hash['k' . $list[$i]['idperson']] : 0;
    $list[$i]['mentions_dif'] = $list[$i]['mentions'] -
        $list[$i]['mentions_prev'];
  }
  return $list;
}


/**
 * Given a list of people produced by getMostPresentInNews, also add the
 * information about where they were last week.
 * @param $list
 * @return Array The list with the modified stuff.
 */
function newsAddMostRecentArticle($list, $what = NULL) {
  // first, extract the list of id's.
  $ids = array();
  for ($i = 0; $i < sizeof($list); $i++) {
    $ids[] = $list[$i]['idperson'];
  }

  $newlist = extractMostRecentArticle($ids);

  $hash = array();
  for ($i = 0; $i < sizeof($newlist); $i++) {
    $hash['k' . $newlist[$i]['idperson']] = $newlist[$i];
  }

  for ($i = 0; $i < sizeof($list); $i++) {
    $list[$i]['article_title'] = $hash['k' . $list[$i]['idperson']]['title'];
    $list[$i]['article_time'] = $hash['k' . $list[$i]['idperson']]['time'];
    $list[$i]['article_link'] = $hash['k' . $list[$i]['idperson']]['link'];
  }
  return $list;
}


/**
 * Given a list of id's, return a list with the most recent article for each of
 * the id in the list.
 * @param $ids
 * @return Array
 */
function extractMostRecentArticle($ids, $source = 'mediafax') {
  $set = implode(",", $ids);
  $sql = "
    SELECT a.title, a.link, a.time, p.idperson
    FROM news_people AS p
    LEFT JOIN news_articles AS a ON p.idarticle = a.id
    WHERE p.idperson IN ($set) AND
          a.source = '$source' AND
          a.source != 'ugc'
    ORDER BY a.time ASC
  ";
  $hash = array();
  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    if (!$hash[$r['idperson']] || $hash[$r['idperson']]['time'] < $r['time']) {
      $hash[$r['idperson']] = $r;
    }
  }

  $res = array();
  foreach ($hash as $el) {
    $res[] = $el;
  }
  return $res;
}


/**
 * Given a list of people produced by getMostPresentInNews, also add the
 * information about where they were last week.
 * @param $list
 * @return Array The list with the modified stuff.
 */
function decorateListWithExtraInfo($list, $year, $mod, $table, $field) {
  // first, extract the list of id's.
  $ids = array();
  for ($i = 0; $i < sizeof($list); $i++) {
    $ids[] = $list[$i]['idperson'];
  }

  $newlist = extractExtraInfo($ids, $year, $mod, $table, $field);

  $hash = array();
  for ($i = 0; $i < sizeof($newlist); $i++) {
    $hash['k' . $newlist[$i]['idperson']] = $newlist[$i][$field];
  }

  for ($i = 0; $i < sizeof($list); $i++) {
    $list[$i][$field] = $hash['k' . $list[$i]['idperson']] ?
        $hash['k' . $list[$i]['idperson']] : 0.0000001;
  }
  return $list;
}


/**
 * Given a list of people produced by getMostPresentInNews, also add the
 * information about where they were last week.
 * @param $list
 * @return Array The list with the modified stuff.
 */
function extractExtraInfo($ids, $year, $mod, $table, $field) {
  $set = implode(",", $ids);
  $sql = "
    SELECT e.idperson, e.{$field}
    FROM {$mod}_{$year}_{$table} AS e
    WHERE e.idperson IN ({$set})
  ";
  $res = array();
  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    $res[] = $r;
  }
  return $res;
}


/**
 * Returns a list of most recent articles from the news tables.
 * @param $mod
 * @param $year
 * @param $count
 * @param {Array.<Number>} $restrict_to_ids The list of person ids that I
 *     should restrict this search for news to. Only get news for this list
 *     of people. Usually used for passing in the list of people one user
 *     choses to follow, but could be used for other lists in the future as
 *     well.
 * @return unknown_type
 */
function getMostRecentNewsArticles($mod, $year, $count, $source = 'mediafax',
                                   $restrict_to_ids = NULL) {
  $where_clause = '';
  if ($mod != NULL && $year != NULL) {
    $where_clause = "AND h.what = '{$mod}/{$year}'";
  }
  if ($restrict_to_ids) {
    $ids = implode(",", $restrict_to_ids);
    $where_clause .= " AND p.idperson in ($ids)";
  }

  if ($source != 'ugc') {
    $where_clause .= " AND a.source != 'ugc'";
  }

  $s = mysql_query("
    SELECT a.id, a.title, a.link, a.time, a.place, a.photo, p.idperson, a.source
    FROM news_people AS p
    LEFT JOIN news_articles AS a ON p.idarticle = a.id
    LEFT JOIN people_history AS h
      ON h.idperson = p.idperson
    WHERE a.source LIKE '$source'
      {$where_clause}
    GROUP BY a.id
    ORDER BY a.time DESC
    LIMIT 0, $count");

  $news = array();
  while ($r = mysql_fetch_array($s)) {
    $r['people'] = getPeopleForNewsId($r['id'], $restrict_to_ids);
    $r['above_six'] = count($r['people']) - 6;
    $news[] = $r;
  }
  return $news;
}


function extractDomainFromLink($link) {
  $domain = parse_url($link, PHP_URL_HOST);
  if (startsWith($domain, "www.")) {
    $domain = substr($domain, 4);
  }
  return $domain;
}


/**
 * Returns a list of most recent articles from the news tables.
 * @param $count
 * @param {Array.<Number>} $restrict_to_ids The list of person ids that I
 *     should restrict this search for news to. Only get news for this list
 *     of people. Usually used for passing in the list of people one user
 *     choses to follow, but could be used for other lists in the future as
 *     well.
 * @return unknown_type
 */
function getMostRecentUgcLinks($count, $restrict_to_ids=NULL, $uid=0,
                               $since=0, $linkId=NULL, $order_by=NULL) {
  $where_clause = '';
  if ($restrict_to_ids) {
    $ids = implode(",", $restrict_to_ids);
    $where_clause .= " AND p.idperson in ($ids)";
  }

  $user_restrict = '';
  if ($uid > 0) {
    $user_restrict = " AND nq.user_id = {$uid}";
  }

  if ($linkId != NULL) {
    $where_clause = "AND a.id = {$linkId}";
  }

  if (!$order_by) {
    $order_by = "a.score DESC, a.votes DESC, a.time DESC";
  }

  $s = mysql_query("
    SELECT
        a.id, a.title, a.link, a.time, a.place, a.photo, p.idperson,
        a.source, a.votes, nq.user_name
    FROM news_people AS p
    LEFT JOIN news_articles AS a ON p.idarticle = a.id
    LEFT JOIN news_queue AS nq ON nq.link = a.link
    WHERE a.source = 'ugc' AND a.time > $since
      {$where_clause}
      {$user_restrict}
    GROUP BY a.id
    ORDER BY {$order_by}
    LIMIT 0, $count");

  $news = array();
  while ($r = mysql_fetch_array($s)) {
    $r['people'] = getPeopleForNewsId($r['id'], $restrict_to_ids);
    $r['above_six'] = count($r['people']) - 6;

    $r['source'] = extractDomainFromLink($r['link']);
    $r['human_time_diff'] =
        human_time_diff((int)$r['time'], time());

    $news[] = $r;
  }
  return $news;
}


/**
 * Counts the number of news in the past N days.
 * @param $days
 * @return unknown_type
 */
function countAllMostRecentNews($days, $source = 'mediafax') {
  $from = time() - 60 * 60 * 24 * $days;
  $s = mysql_query("
    SELECT a.title
    FROM news_articles AS a
    WHERE a.time > {$from} AND a.source = '$source' AND a.source != 'ugc'");
  return mysql_num_rows($s);
}


/**
 * Returns a list of ids for the people that show up in a news item.
 * @param {number} id The id of the news item.
 * @return Array The array of persons ids.
 */
function getPeopleForNewsId($id, $highlight_ids=NULL) {
  global $followPeopleHashById;

  $s = mysql_query("
    SELECT idperson, name, display_name
    FROM news_people AS p
    LEFT JOIN people ON people.id = p.idperson
    WHERE idarticle={$id}");

  $res = array();
  while($r = mysql_fetch_array($s)) {
    $r['name'] = str_replace(' ', '+', $r['name']);

    $r['following'] = array_key_exists($r['idperson'], $followPeopleHashById);
    if ($highlight_ids) {
      $r['highlight'] = in_array($r['idperson'], $highlight_ids);
    }

    // Stuff that we are following or is highlighted should be pused at the
    // beginning of the array.
    if ($r['following'] || $r['highlight']) {
      array_unshift($res, $r);
    } else {
      $res[] = $r;
    }
  }
  return $res;
}

?>