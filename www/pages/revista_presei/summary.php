<?php

include_once("hp-includes/electoral_colleges.php");

$t = new Smarty();

if (isSet($_GET['college_restrict'])) {
  $college_name = str_replace("+", " ", $_GET['college_restrict']);
  $college_name = mysql_real_escape_string($college_name);

  $people_restrict = getCollegePeopleIds($college_name, "2012");
  $news = getMostRecentNewsArticles(NULL, NULL, 10, '%', $people_restrict);

  $t->assign('restrict',
             "Doar știrile pentru candidați în colegiul " .
             "<b>{$college_name}</b>");

} else {
  $news = getMostRecentNewsArticles(NULL, NULL, 10, '%');
}

$t->assign('news', $news);
$t->display('revista_presei_news_list.tpl');

?>
