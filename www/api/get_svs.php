<?php

include ('../_top.php');
include ('../functions.php');

$E = trim(mysql_real_escape_string($_GET['e']));
$W = trim(mysql_real_escape_string($_GET['w']));
$N = trim(mysql_real_escape_string($_GET['n']));
$S = trim(mysql_real_escape_string($_GET['s']));
$Z = trim(mysql_real_escape_string($_GET['z']));
$sv = trim(mysql_real_escape_string($_GET['sv']));

if ($Z >10 && $E > 0 && $W > 0 && $N > 0 && $S > 0) {
	$q = "SELECT group_concat(concat(nr_sv, '-', nr_cir)) AS svs, institutie, adresa, lat, lon
		FROM sectii_vot 
		WHERE lat BETWEEN $S AND $N AND lon BETWEEN $W AND $E
		GROUP BY institutie, adresa, lat, lon";
	$s = mysql_query($q);
	
	$output = array();
	while ($sv = mysql_fetch_array($s)) {
		$output[] = $sv;
	}

	echo json_encode($output);
}
else if (isset($sv)) {
	$data = explode("-", $sv);
	#echo $data[0];

	$q = "SELECT artera FROM sectii_vot_detalii
		WHERE nr_cir = $data[1] and nr_sv = $data[0]
		ORDER BY artera";
	$s = mysql_query($q);
	
	$output = "";
	while ($sv = mysql_fetch_array($s)) {
		$output .= "<div> " . $sv[0] . "</div>";
	}
	echo $output;

}

include ('../_bottom.php');

?>
