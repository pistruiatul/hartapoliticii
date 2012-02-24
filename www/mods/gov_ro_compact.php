
<?php
$s = mysql_query("SELECT * FROM govro_people WHERE idperson = {$person->id}");
$r = mysql_fetch_array($s);

echo "{$r['title']}.<br>";
echo "Mai multe detalii, <a href=\"{$r['link']}\">pe site-ul guvernului</a>.";

?>