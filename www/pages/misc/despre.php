<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}


$title = "Despre acest site / Contact";
$nowarning = true;

include('header.php');

$t = new Smarty();
$t->display('about.tpl');

?>
