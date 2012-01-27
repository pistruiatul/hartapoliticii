<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  if (!$_GET['id']) {
    header('Location: http://www.vivi.ro/politica');
  }
  header('Location: http://www.vivi.ro/politica/?cid=7&id=' . $_GET['id']);
}


if (!(floor($_GET['id']) > 0)) {
  die("Wrong id");
}
$idperson = getSenatorPersonId($_GET['id']);
header('Location: http://www.vivi.ro/politica/?cid=9&id=' . $idperson);
?>
