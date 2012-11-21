<?php
// This script looks over the links submitted to the Community page in the
// past 72 hours - 3 days - and computes a score for each.
//
// Right now the score will be a combination of total number of votes, total
// number of comments, and time since it was submitted.
//
// TODO(vivi): The scoring of these links should be improved more over time
// if we notice it's not working properly.

include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/ugc_utils.php');


function calculateArticleScore($id, $submitTime) {
  // Get the number of votes.
  $votes = getCountVotesForArticle($id) + 1.0;

  $hours_since_submit = (time() - $submitTime) / 3600;

  if ($hours_since_submit > 72) {
    $score = $votes / 100.0;
  } else {
    $score = $votes * (1.0 - $hours_since_submit / 72.0);
  }

  echo $id . ' new score ' . $score . '\n';

  mysql_query("
    UPDATE news_articles SET score = {$score}
    WHERE id = {$id}
  ");
}


$s = mysql_query("
    SELECT * FROM news_articles
    WHERE source = 'ugc'
    AND time > " . (time() - 86400 * 3) . "
");

while ($r = mysql_fetch_array($s)) {
  calculateArticleScore($r['id'], $r['time']);
}

include ('../_bottom.php');
?>
