<?
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

function getGovroPersonId($name, $link, $title, $time) {
  $s = mysql_query("SELECT id FROM govro_people WHERE name='$name'");
  if ($r = mysql_fetch_array($s)) {
    // Update the max time this person has been here.
    mysql_query("UPDATE govro_people SET maxtime=$time WHERE id={$r['id']}");
    return $r['id'];

  } else {
    $si = mysql_query("INSERT INTO govro_people(name, link, title, mintime)
        VALUES('$name', '$link', '$title', '$time')");
    return mysql_insert_id();
  }
}

$time = time();
$name = trim($_POST['name']);
$link = trim($_POST['link']);
$title = trim($_POST['title']);

if ($name && $link && $title) {
  $id = getGovroPersonId($name, $link, $title, $time);
}
?>
