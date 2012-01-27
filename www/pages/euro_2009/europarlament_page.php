<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: http://www.vivi.ro/politica');
}

$title = "Alegeri EuroParlamentare 2009";
$nowarning = true;
include('header.php');
include('pages/euro_2009/europarlament_functions.php');
include('hp-includes/people_lib.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'euro/2009';
$c = 'alegeri+europarlamentare+2009';
include('pages/submenu.php');

?>

