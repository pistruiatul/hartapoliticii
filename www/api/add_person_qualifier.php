<?php
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

function addPersonQualifier($idarticle, $link, $source, $idperson, $name, $q, $source) {
  $s = mysql_query("
      SELECT id FROM news_qualifiers WHERE name='$name' AND idarticle='$idarticle'");

  if (mysql_num_rows($s) == 0) {
    $si = mysql_query("
        INSERT INTO news_qualifiers(idarticle, idperson, name, qualifier)
        VALUES($idarticle, $idperson, '$name', '$q')");
  }
}

$name = trim($_POST['name']);
$link = trim($_POST['link']);
$idperson = trim($_POST['idperson']);
$idarticle = trim($_POST['idarticle']);

$q = trim($_POST['q']);
$source = trim($_POST['source']);


if ($name && $link && $q) {
  $id = addPersonQualifier($idarticle, $link, $source, $idperson, $name, $q, $source);
}

include ('../_bottom.php');
?>
