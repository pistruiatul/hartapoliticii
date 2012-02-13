<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  if (!$_GET['id']) {
    header('Location: /');
  }
  header('Location: /?cid=7&id=' . $_GET['id']);
}


if($idperson = getSenatorPersonId((int)$_GET['id'])){
	header('Location: /?cid=9&id=' . $idperson);
}else{
	die("Wrong id");
}
?>
