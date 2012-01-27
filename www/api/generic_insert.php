<?php
include_once('../secret/api_key.php');

include('../_top.php');

$template_query = "INSERT IGNORE INTO {table_name}({fields}) VALUES({values})";

$fields = array();
$values = array();

// Make up the query first.
foreach ($_POST as $key => $value) {
	$fields[] = mysql_real_escape_string($key);
	$values[] = "'" . mysql_real_escape_string($value) . "'";
}

$table = $_GET['table'];

if ($table != 'yt_videos') {
	die('Boo!');
}

$template_query = str_replace('{fields}', join(',', $fields), $template_query);
$template_query = str_replace('{values}', join(',', $values), $template_query);
$template_query = str_replace('{table_name}', $table, $template_query);

mysql_query($template_query) || die('failed');
echo 'thanks: ' . join(',', $values);

include('../_bottom.php');
?>
