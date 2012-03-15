<?
require_once('../secret/db_user.php');
require_once('../_top.php');

// Load wp-config so that we can use the fact that the user is logged in.
require_once('../wp-config.php');

include_once('../hp-includes/people_lib.php');
include_once('../hp-includes/user_utils.php');
include_once('../hp-includes/declarations.php');

/**
 * Insert the highilight in the database.
 */
function insertHighlight($uid, $declarationId, $startWord, $endWord, $content) {
  // Find the declaration link.
  $declarationLink = getDeclarationLink($declarationId);
  if (!$declarationLink) die("Wrong declaration id");

  $sql = "
      INSERT INTO people_declarations_highlights
      (source, user_id, start_word, end_word, content)
      VALUES
      ('{$declarationLink}', {$uid}, {$startWord}, {$endWord}, '{$content}')";

  mysql_query($sql);
}

/**
 * Insert the highilight in the database.
 */
function deleteHighlight($uid, $declarationId, $startWord, $endWord, $content) {
  // Find the declaration link.
  $declarationLink = getDeclarationLink($declarationId);
  if (!$declarationLink) die("Wrong declaration id");

  $sql = "
      DELETE FROM people_declarations_highlights
      WHERE
        source = '{$declarationLink}' AND
        user_id = {$uid} AND
        start_word = {$startWord} AND
        end_word = {$endWord} AND
        user_id = {$uid}";

  mysql_query($sql);
}


// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

if ($uid == 0) die("You're not logged in");

// Sanitize the inputs a little bit.
$declarationId = (int)$_GET['declaration_id'];
$startWord = (int)$_GET['start_word'];
$endWord = (int)$_GET['end_word'];
$action = $_GET['action'];

// The content is duplicated from what's in the declaration itself, but we
// want to do this in case in some future we start correcting or editing
// declarations, in which case we'll need to realign the highlights.
$content = mysql_real_escape_string($_GET['content']);

if ($action == 'add') {
  insertHighlight($uid, $declarationId, $startWord, $endWord, $content);
} else {
  deleteHighlight($uid, $declarationId, $startWord, $endWord, $content);
}
// Also record this in the moderation queue so we can see who added what.
$ip = $_SERVER['REMOTE_ADDR'];
$userLogin = getUserLogin($uid);
$personId = getPersonId($declarationId);

mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('highlight', {$personId}, 'highlight by {$userLogin}', '$ip',
          ". time() . ")");

echo "OK";

require_once('../_bottom.php');
?>
