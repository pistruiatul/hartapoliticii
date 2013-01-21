<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}
$title = "Camera deputatilor 2008 - 2012";

include('header.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'cdep/2008';
$c = 'camera+deputatilor+2009';
include('pages/submenu.php');

?>

