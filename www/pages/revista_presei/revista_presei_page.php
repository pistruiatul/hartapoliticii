
<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}
$title = "Revista Presei";

include('header.php');
include('hp-includes/people_lib.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'revista/presei';
include('pages/submenu.php');
?>
