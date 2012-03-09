<?
include_once('../secret/api_key.php');

include ('../_top.php');
include ('../functions.php');
include_once('../hp-includes/people_lib.php');

$idperson = (int)$_POST['idperson'];
$time = trim($_POST['time']);
$source = mysql_real_escape_string($_POST['source']);
$declaration = mysql_real_escape_string($_POST['declaration']);

if ($idperson > 0) {
  mysql_query("
      INSERT IGNORE
      INTO people_declarations(idperson, source, time, declaration)
      VALUES({$idperson}, '{$source}', {$time}, '{$declaration}')");
}

include ('../_bottom.php');
?>
