<?php

function getCountVotesForArticle($articleId) {
  $s = mysql_query("
    SELECT sum(vote) as cnt
    FROM news_votes
    WHERE article_id = {$articleId}
  ");

  $r = mysql_fetch_array($s);
  return $r['cnt'];
}


?>