<?
/**
 * Tests the existance of a wordpress post for this person. If a post does not
 * exist, a post is created.
 * @param {Person} person The person object for which we are checking this.
 * @return {number} The id of the post row in the database
 */
function checkWordpressPostExistance($person) {
  $s = mysql_query(
    "SELECT * FROM wp_posts
     WHERE guid LIKE '%/politica/?cid=9&id={$person->id}'");
  if (mysql_num_rows($s) == 0) {
    // '" . date("Y-m-d H:i:s") . "',
    // '" . gmdate("Y-m-d H:i:s") . "',
    mysql_query(
      "INSERT INTO wp_posts(post_author,
          post_content, post_title, post_name, post_category, post_excerpt,
          post_status, to_ping, pinged, post_content_filtered,
          guid, post_mime_type)
      VALUES(
          1,
          '{$person->name}',
          '{$person->displayName}',
          '" . str_replace(" ", "-", $person->name) . "',
          8,
          '',
          'publish',
          '',
          '',
          '',
          'http://www.hartapoliticii.ro/?cid=9&id={$person->id}',
          ''
        )");
    return mysql_insert_id();
  } else {
    $r = mysql_fetch_array($s);
    return $r['ID'];
  }
}

/**
 * Displays the wordpress posts and the form on a page other than wordpress
 * itself.
 */
function displayWordpressComments($postid) {
  echo "Hi there " . $postid;
}
?>