<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}
$title = "Alegeri 2012 Camera Deputaților";

include('header.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'senat/2012';
$c = 'senat+2012';
include('pages/submenu.php');

?>

