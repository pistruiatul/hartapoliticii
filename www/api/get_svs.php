<?php

include ('../_top.php');
include ('../functions.php');

$E = trim(mysql_real_escape_string($_GET['e']));
$W = trim(mysql_real_escape_string($_GET['w']));
$N = trim(mysql_real_escape_string($_GET['n']));
$S = trim(mysql_real_escape_string($_GET['s']));
$Z = trim(mysql_real_escape_string($_GET['z']));

#$Z >= 13
$output = array();
if ($Z >10 && $E > 0 && $W > 0 && $N > 0 && $S > 0) {
	#$q = "SELECT nr_col_cd, nr_col_s, group_concat(nr_sv) AS svs, institutie, adresa, lat, lon
	#	WHERE lat > $S AND lon > $W AND lat < $N AND lon < $E
	$q = "SELECT group_concat(concat(nr_sv, '-', nr_col_s, '-', nr_col_cd)) AS svs, institutie, adresa, lat, lon
		FROM sectii_vot 
		WHERE lat BETWEEN $S AND $N AND lon BETWEEN $W AND $E
		GROUP BY institutie, adresa, lat, lon";
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
