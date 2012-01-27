<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://www.hartapoliticii.ro');

include('hp-includes/party_class.php');

// -------------------------- Initialization stuff --------------------
$id = $_GET['id'] ? (int)$_GET['id'] : 0;
if ($id == 0) return;

$party = new Party($id);

// --------------------------- Display Header ---------------------
$title = $party->longName;
$nowarning = true;
include('header.php');

// ---------------------------- Display Breadcrumbs ---------------
$t = new Smarty();
$t->assign('name', $party->longName);

$crumbs = array();
$crumbs[] = array(
  'name' => 'Sumar',
  'href' => '?cid={$cid}&id={$party->id}'
);
$t->assign('crumbs', $crumbs);
$t->display('person_top_bar.tpl');

// ----------------------------- Left sidebar----------- ----------
echo '<table width=970><td valign=top width=340>';
$t = new Smarty();

$t->assign('logo', $party->getLogo());
list($width, $height, $type, $attr) = getimagesize($party->getLogo());
$t->assign('logo_width', min(250, $width));

$t->display('party_left_sidebar.tpl');


// ----------------------------- Display Actual Content ----------
echo '</td><td valign="top">';

// Grab the list of mods to display from the party_history database and locate
// those mods.
$history = $party->getHistory();
foreach ($history as $item) {
  if (file_exists("mods_parties/{$item}/mod_compact.php")) {
    $moduleTitle = $party->getLongTitleForWhat($item);

    echo "<div class=module>";
    echo "<div class=moduletitle>$moduleTitle</div>";

    echo "<div class=modulecontent>";
    include("mods_parties/{$item}/mod_compact.php");
    echo "</div>";
    echo "</div>";
  }
}

// ----------------------------- Comments ----------

echo '</td></table>';
?>
