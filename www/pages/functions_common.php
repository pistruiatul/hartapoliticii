<?php
/**
 * Returns an array of people given an SQL query. This is a private helper
 * method only for this file.
 * @param $sql The sql query.
 * @return Array
 * @private
 */
function getMostPresentInNews($count, $what = NULL, $tstart = NULL,
                              $tend = NULL, $for_ids = NULL,
                              $source = 'mediafax') {
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

  $restrict = "";
  if ($for_ids != NULL) {
    $restrict = "AND p.idperson IN (" . implode(",", $for_ids) . ")";
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
          a.source LIKE '$source'
        {$restrict} {$what_filter}
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
    WHERE p.idperson IN ($set) AND a.source = '$source'
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
 * @return unknown_type
 */
function getMostRecentNewsArticles($mod, $year, $count, $source = 'mediafax') {
  $where_clause = '';
  if ($mod != NULL && $year != NULL) {
    $where_clause = "AND h.what = '{$mod}/{$year}'";
  }

  $s = mysql_query("
    SELECT a.id, a.title, a.link, a.time, a.place, p.idperson, a.source
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
    $r['people'] = getPeopleForNewsId($r['id']);
    $news[] = $r;
  }
  return $news;
}


/**
 * Returns a list of ids for the people that show up in a news item.
 * @param {number} id The id of the news item.
 * @return Array The array of persons ids.
 */
function getPeopleForNewsId($id) {
  $s = mysql_query("
    SELECT idperson, name, display_name
    FROM news_people AS p
    LEFT JOIN people ON people.id = p.idperson
    WHERE idarticle=$id");
  $res = array();
  while($r = mysql_fetch_array($s)) {
    $r['name'] = str_replace(' ', '+', $r['name']);
    $res[] = $r;
  }
  return $res;
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
    WHERE a.time > {$from} AND a.source = '$source'");
  return mysql_num_rows($s);
}


/**
 * Given an id, looks on disk for the tiny picture of this person.
 * Returns the default non-photo for people that don't have a tiny picture.
 * @param $id
 * @return unknown_type
 */
function getTinyImgUrl($id) {
  $img = "images/people_tiny/{$id}.jpg";
  if (is_file($img)) {
    $fname = "images/people_tiny/{$id}.jpg";
    $count = 1;
    // Get the most recent file we have for this person.
    while (is_file($fname)) {
      $img = $fname;
      $fname = "images/people_tiny/{$id}_{$count}.jpg";
      $count++;
    }
  } else {
    return "images/tiny_person.jpg";
  }
  return $img;
}


/**
 * For a given vote and table, returns a list (an array?) of tags associated
 * with it, added by the user with the given user id.
 * @param {string} $table The table where the votes are.
 * @param {int} $idvote The id of the vote we are looking for.
 * @param {int} $uid The id of the user that added the tags.
 * @return {string} For now, a comma separated string with the tags.
 */
function getVoteTags($table, $idvote, $uid) {
  $s = mysql_query("
    SELECT tags.tag, tags.id, tagged.inverse
    FROM parl_tagged_votes AS tagged
    LEFT JOIN parl_tags AS tags ON tags.id = tagged.idtag
    WHERE
        tagged.idvote = {$idvote} AND
        tagged.uid = {$uid} AND
        tagged.votes_table = '{$table}'
  ");

  $tags = array();
  while ($r = mysql_fetch_array($s)) {
    $tags[] = $r;
  }
  return $tags;
}


/**
 * Retrieves the most recent blog posts from the blog.
 * @return unknown_type
 */
function getMostRecentBlogPosts($count) {
  $s = mysql_query("
    SELECT id, post_title, comment_count, UNIX_TIMESTAMP(post_date) AS d
    FROM wp_posts
    WHERE post_status = 'publish'
    ORDER BY post_date DESC
    LIMIT 0, {$count}
  ");
  $posts = array();
  while ($r = mysql_fetch_array($s)) {
    $posts[] = $r;
  }
  return $posts;

}

?>