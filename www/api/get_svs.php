<?php
#include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
#include_once('../hp-includes/people_lib.php');

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

$E = trim(mysql_real_escape_string($_GET['e']));
$W = trim(mysql_real_escape_string($_GET['w']));
$N = trim(mysql_real_escape_string($_GET['n']));
$S = trim(mysql_real_escape_string($_GET['s']));
$Z = trim(mysql_real_escape_string($_GET['z']));

#$Z >= 13
$output = array();
if ($E > 0 && $W > 0 && $N > 0 && $S > 0) {
	$q = "SELECT nr_col_cd, nr_col_s, group_concat(nr_sv) AS svs, institutie, adresa, lat, lon
		FROM sectii_vot 
		WHERE lat > $S AND lon > $W AND lat < $N AND lon < $E
		GROUP BY institutie, adresa, nr_col_cd, nr_col_s, lat, lon";
	#echo $q;
	$s = mysql_query($q);
	while ($sv = mysql_fetch_array($s)) {
		$output[] = $sv;
	}

}
#echo $E;

#$output = Array($S, $W, $N, $E);
echo json_encode($output);

include ('../_bottom.php');

?>
