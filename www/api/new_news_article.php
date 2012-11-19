<?php
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

function getArticleId($time, $place, $link, $title, $photo, $source) {
  $time = (int)$time;

  $s = mysql_query("SELECT id FROM news_articles WHERE link='$link'");
  if ($r = mysql_fetch_array($s)) {
    return $r['id'];
  } else {
    $votes = $source == 'ugc' ? 1 : 0;

    mysql_query("
      INSERT INTO news_articles(time, place, link, title, photo, source, votes)
      VALUES($time, '$place', '$link', '$title', '$photo', '$source', $votes)");

    return mysql_insert_id();
  }
}

$idperson = trim(mysql_real_escape_string($_POST['id']));
$time = trim(mysql_real_escape_string($_POST['time']));
$place = trim(mysql_real_escape_string($_POST['place']));
$link = trim(mysql_real_escape_string($_POST['link']));
$title = trim(mysql_real_escape_string($_POST['title']));
$source = trim(mysql_real_escape_string($_POST['source']));
$photo = trim(mysql_real_escape_string($_POST['photo']));

$idarticle = trim($_POST['idarticle']);
if (!$idarticle) {
  $idarticle = getArticleId($time, $place, $link, $title, $photo, $source);
}

if ($idperson > 0) {
  mysql_query("INSERT IGNORE INTO news_people(idperson, idarticle)
      VALUES($idperson, $idarticle)");
}
echo $idarticle;

include ('../_bottom.php');
?>
