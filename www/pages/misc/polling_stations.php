<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: /');

$title = "Hartă cu secțiile de votare, Alegeri 2012";


include('header.php');

$t = new Smarty();

$t->display('polling_stations.tpl');

?>
