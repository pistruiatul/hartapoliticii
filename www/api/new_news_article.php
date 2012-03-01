<?
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

function getArticleId($time, $place, $link, $title, $photo, $source) {
  $s = mysql_query("SELECT id FROM news_articles WHERE link='$link'");
  if ($r = mysql_fetch_array($s)) {
    return $r['id'];
  } else {
    mysql_query(
      "INSERT INTO news_articles(time, place, link, title, photo, source)
      VALUES($time, '$place', '$link', '$title', '$photo', '$source')");
    return mysql_insert_id();
  }
}

$idperson = trim($_POST['id']);
$time = trim($_POST['time']);
$place = trim($_POST['place']);
$link = trim($_POST['link']);
$title = trim($_POST['title']);
$source = trim($_POST['source']);
$photo = trim($_POST['photo']);

$idarticle = trim($_POST['idarticle']);
if (!$idarticle) {
  $idarticle = getArticleId($time, $place, $link, $title, $photo, $source);
}

if ($idperson > 0) {
  mysql_query("INSERT IGNORE INTO news_people(idperson, idarticle)
      VALUES($idperson, $idarticle)");
}
echo $idarticle;

?>
