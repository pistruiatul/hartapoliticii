<?php 
include_once('pages/functions_common.php');

$s = mysql_query("
    SELECT q.id, qualifier, idperson, p.display_name, n.time, n.source, n.link
    FROM news_qualifiers AS q
    LEFT JOIN news_articles AS n ON n.id = q.idarticle
    LEFT JOIN people AS p ON p.id = q.idperson
    WHERE q.approved = 1 AND q.idperson > 0
    GROUP BY qualifier
    ORDER BY n.time DESC
    LIMIT 0, 50");

$facts = array();
while ($r = mysql_fetch_array($s)) {
	$guy = $r;
	$guy['tiny_photo'] = getTinyImgUrl($r['idperson']);
	$facts[] = $guy;
}

$t = new Smarty();
$t->assign('facts', $facts);
$t->display('pres_2009_learned_facts.tpl');
?>
