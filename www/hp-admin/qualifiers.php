<?php
include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');
include ('../smarty/Smarty.class.php');

$month_ago = time() - 30 * 24 * 60 * 60;

$sql = mysql_query("
  SELECT q.name, q.idperson, q.qualifier, count(*) as num, a.link
  FROM news_qualifiers AS q
  LEFT JOIN news_articles AS a ON a.id = q.idarticle
  WHERE q.approved = 0 AND a.time > {$month_ago}
  GROUP BY concat(q.name, q.qualifier)
  ORDER BY num DESC, q.idperson DESC
  ");
$qualifiers = array();
while ($r = mysql_fetch_array($sql)) {
  $qualifiers[] = $r;
}

$t = new Smarty();
$t->assign('qualifiers', $qualifiers);
$t->display('admin_qualifiers.tpl');

?>
