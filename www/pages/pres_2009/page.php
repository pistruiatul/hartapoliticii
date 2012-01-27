<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: http://www.hartapoliticii.ro');
}
$title = "Alegeri Prezidențiale 2009";

$nowarning = true;
include('header.php');
include('hp-includes/people_lib.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'pres/2009';
$c = 'prezidentiale+2009';
include('pages/submenu.php');

?>
