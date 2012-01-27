<?
include('../_top.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?
echo '<b>Photos</b><br>';

if ($_POST['action'] == 'addphoto') {
  // get the third slash from the url.
  $url = $_POST['photo'] ? $_POST['photo'] : $_POST['orig_url'];

  $parts = explode(".", $url);
  $ext = strtolower(array_pop($parts));

  if (strlen($ext) > 4) {
    $ext = 'jpg';
  }
  // Now actually get the photo
  $ch = curl_init($url);

  $fname = "../images/people/{$_POST['pid']}.$ext";
  $count = 1;
  while (is_file($fname)) {
    $fname = "../images/people/{$_POST['pid']}_$count.$ext";
    $count++;
  }

  $fp = fopen($fname, "w");

  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);

  curl_exec($ch);
  curl_close($ch);
  fclose($fp);

  echo "Saved as <a href=\"$fname\">$fname</a>. ";
  echo "Go to <a href=/?cid=9&id={$_POST['pid']}>his page</a>.";

  mysql_query(
    "update moderation_queue set state=2 where id={$_POST['id']}");
}

// See if there is already a photo to approve or delete
if ($_GET['action'] == 'delete') {
  mysql_query(
    "update moderation_queue set state=1 where id={$_GET['id']}");
}

echo "<br>";
// get the photos.
$s = mysql_query(
  "SELECT m.id, value, type, display_name, m.idperson
  FROM moderation_queue AS m
  LEFT JOIN people AS p ON p.id = m.idperson
  WHERE state = 0
  ORDER BY time DESC");

while ($r = mysql_fetch_array($s)) {
  $url = $r['value'];
  $type = $r['type'];
  $name = $r['display_name'];
  $pid = $r['idperson'];
  $id = $r['id'];
  echo "<a href=/?cid=9&id=$pid>$name</a>: <a href=$url>$url</a> - ";

  echo "<a href=admin.php?id=$id&action=delete>delete</a><br>";
  echo "<blockquote>";
  echo "<form action=admin.php method=POST>
        img: <input type=text name=photo size=50>
        <input type=submit value=go>
        <input type=hidden name=orig_url value=\"$url\">
        <input type=hidden name=id value=$id>
        <input type=hidden name=pid value=$pid>
        <input type=hidden name=action value=addphoto>
        </form>";

  echo "</blockquote>";
}


echo '<br><br><b>Recent searches</b><br><pre>';
$s = mysql_query(
  "SELECT * FROM log_searches ORDER BY time DESC LIMIT 0, 50");
while ($r = mysql_fetch_array($s)) {
  $q = $r['query'];
  echo $r['ip'] . "\t" . date("M d H:i:s", $r['time']) . " " .
       "\t<a href=\"/?cid=search&q=$q\">" . htmlspecialchars($q) ."</a> (" .
       $r['num_results'] . ')';
  if ($r['found'] != -1) {
    echo "\t<font color=green>OK - ".($r['found']+1)."</font>";
  }
  echo '<br>';
}
echo "</pre>";
include('../_bottom.php');
?>