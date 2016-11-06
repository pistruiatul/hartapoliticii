<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}
$title = "Alegeri 2016 Camera Deputaților";

include('header.php');
include('hp-includes/news.php');

$page = 'cdep/2016';
$c = 'camera+deputatilor+2016';
include('pages/submenu.php');

?>

