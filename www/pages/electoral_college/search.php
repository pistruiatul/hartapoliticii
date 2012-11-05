<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

$title = "Căutare Colegiu Uninominal Alegeri 2012";
include('header.php');

$t = new Smarty();
$t->display('electoral_college_search.tpl');
?>
