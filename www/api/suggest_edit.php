<?
// Adds a key/value pair to a certain person in the moderation queue. This
// is currently used by the form where users can suggest a photo of a
// politician. That form eventually ends up here, with a key='photo' and the
// value being the photo url.
//
// An administrator can later on process this suggestion and approve the photo.
//
// Even though for now this is only used for photos, it could eventually be
// used for other suggested edits to a person's page (like changing the
// contact information or something).

include ('../_top.php');

// We should have type, pid, and url which means what to take and from where.
$type = mysql_real_escape_string($_GET['type']);
$pid = mysql_real_escape_string($_GET['pid']);
$value = mysql_real_escape_string($_GET['value']);

$ip = $_SERVER['REMOTE_ADDR'];

mysql_query(
  "INSERT INTO moderation_queue(type, idperson, value, ip, time)
   VALUES('$type', $pid, '$value', '$ip', ". time() . ")");

echo 'hi';

include ('../_bottom.php');
?>
