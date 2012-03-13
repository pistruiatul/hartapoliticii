<?php
/**
 * Functions that deal with editing of content.
 */

/**
 * Given an sql query, returns the ID used for that query. This id will later
 * be used to retrive the content, replace the content, and run that query.
 */
function getContentUpdateId($sql) {
  $query = addslashes($sql);

  $s = mysql_query("SELECT id FROM wiki_edits WHERE query='$query'");
  if ($r = mysql_fetch_array($s)) {
    return $r['id'];
  } else {
    mysql_query("INSERT INTO wiki_edits(query) VALUES('$query')");
    return mysql_insert_id();
  }
}


?>
