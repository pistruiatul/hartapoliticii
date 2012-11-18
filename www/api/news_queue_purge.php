<?php
// This script goes over the links that were submitted by users in news_queue,
// looks at whether they made it to news_articles - which means that our
// crawlers have found people to tag in them - and they change the status of
// the submission in the queue accordingly.
//
// This script assumes that it's called ONLY after we've processed the
// news_queue and hence we can rely on the article being or not being present
// in news_articles as a clear indicator of whether it was a meaningful
// submission or not.

include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');


$s = mysql_query("SELECT * FROM news_queue WHERE status = 0");

while ($r = mysql_fetch_array($s)) {
  $results[] = $r;

  $s2 = mysql_query("SELECT id FROM news_articles WHERE link='{$r["link"]}'");
  $new_status = $r2 = mysql_fetch_array($s2) ? 1 : -1;

  mysql_query("
    UPDATE news_queue SET status = {$new_status}
    WHERE link = '{$r["link"]}'
  ");
}

include ('../_bottom.php');
?>
