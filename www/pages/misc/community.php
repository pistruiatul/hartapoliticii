<?php

include_once("hp-includes/news.php");
include_once("hp-includes/electoral_colleges.php");

$title = "Comunitate";
include('header.php');

$t = new Smarty();

if (isSet($_GET['id'])) {
  $linkId = (int)$_GET['id'];

  $t->assign('news', getMostRecentUgcLinks(1, NULL, 0, 0, $linkId));

  $t->display('pages_misc_community_link.tpl');

} else {
  if (isSet($_GET['college_restrict'])) {
    $college_name = str_replace("+", " ", $_GET['college_restrict']);
    $college_name = mysql_real_escape_string($college_name);

    $people_restrict = getCollegePeopleIds($college_name, "2012");

    $t->assign('restrict',
               "Doar resursele adăugate pentru candidații din colegiul " .
               "<b>" . ucwords($college_name) . "</b>");
  } else {
    $people_restrict = NULL;
  }

  $t->assign('news', getMostRecentUgcLinks(30, $people_restrict, 0));

  $t->display('pages_misc_community.tpl');
}

?>
