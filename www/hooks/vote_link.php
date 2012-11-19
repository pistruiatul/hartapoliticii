<?php
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');


function getCountVotesForArticle($articleId) {
  $s = mysql_query("
    SELECT sum(vote) as cnt
    FROM news_votes
    WHERE article_id = {$articleId}
  ");

  $r = mysql_fetch_array($s);
  return $r['cnt'];
}


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) {
  die("Doar utilizatorii autentificați pot propune link-uri. " .
      "<a href='/wp-login.php?action=login'>Autentifică-te</a>.");
}

$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);

$linkId = mysql_real_escape_string($_GET['articleId']);
$vote = (int)$_GET['vote'] < 0 ? -1 : 1;

// Add the vote in the votes table.
mysql_query("
  INSERT IGNORE INTO news_votes(article_id, user_id, vote, ip, time_ms)
  VALUES({$linkId}, {$uid}, {$vote}, '{$ip}', ". time() . ")
");

// Simply run a query to update the vote too, just in case this was a second
// vote and the insert failed.
mysql_query("
  UPDATE news_votes SET vote = {$vote}
  WHERE article_id = {$linkId} AND user_id = {$uid}
");

// Update the number of votes on the article itself.
$countVotes = getCountVotesForArticle($linkId) + 1;
mysql_query("
  UPDATE news_articles SET votes = {$countVotes}
  WHERE id = {$linkId}
");

echo $countVotes;

require_once('../_bottom.php');
?>
