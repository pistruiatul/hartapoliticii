<?
include_once('../secret/api_key.php');
include ('../_top.php');

$link = $_GET['link'];

$s = mysql_query("SELECT count(*) as cnt
                  FROM people_declarations
                  WHERE source LIKE '{$link}%'");
if ($r = mysql_fetch_array($s)) {
  echo $r['cnt'];
} else {
  echo 0;
}

include ('../_bottom.php');
?>
