<?php
// Certain power users have the possibility to remove people from being
// associated with news articles, for cases where the associations are simply
// wrong, which sometimes happens.
//
// This file is the hook for the JS call from the website to remove that
// association.

require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../hp-includes/user_utils.php');
require_once('../wp-config.php');


/**
 * Remove an association between a person and an article.
 */
function deleteTag($articleId, $personId) {
  $sql = "
      DELETE FROM news_people
      WHERE
        idperson = {$personId} AND
        idarticle = {$articleId}";
  mysql_query($sql);
}


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;
if ($uid == 0) die("You're not logged in");
if (getUserLevel($uid) == 0) die("Not enough privileges");

// Sanitize the inputs a little bit.
$articleId = (int)$_GET['article_id'];
$personId = (int)$_GET['person_id'];

deleteTag($articleId, $personId);

echo "Done";

require_once('../_bottom.php');
?>
