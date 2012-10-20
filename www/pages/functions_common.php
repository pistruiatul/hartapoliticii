<?php

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
