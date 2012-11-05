<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}
$title = "Alegeri 2012 Camera Deputaților";

include('header.php');
include('hp-includes/news.php');

$page = 'cdep/2012';
$c = 'camera+deputatilor+2012';
include('pages/submenu.php');

?>

