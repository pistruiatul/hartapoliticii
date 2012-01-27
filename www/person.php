<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://www.hartapoliticii.ro');

include_once('hp-includes/wiki_edits.php');
include_once('hp-includes/people_lib.php');
include_once('hp-includes/people_util.php');

function isExpand($exp) {
  if ($exp == 'news' || $exp == 'cdep/2008' || $exp == 'senat/2008') {
  	return $exp;
  }
  return NULL;
}

$id = $_GET['id'];

if ($_GET['name']) {
  $query = $_GET['name'];
  $persons = search($query);
  $id = $persons[0]->id;
}

// Given the ID of a person, go through it's history and load all
// the mods (modules) in the respective directories.
$person = new Person();
$person->setId($id);
$person->loadFromDb();

$title = moveFirstNameLast($person->displayName);
$nowarning = true;

?>

<meta property='og:site_name' content='Harta Politicii' />
<meta property='og:website' content='http://hartapoliticii.ro' />
<meta property='og:title' content='<? echo $person->displayName ?> - Harta politicii din România' />
<meta property='fb:app_id' content='205183855930' />

<!-- Update your html tag to include the itemscope and itemtype attributes -->
<html itemscope itemtype="http://schema.org/Person">
<!-- Add the following three tags inside head -->
<meta itemprop="name" content="<? echo $person->displayName ?> - Harta Politicii din România">

<?

include('header.php');

$history = $person->getHistory();

// ------------ Render the top bar, with the person name and bread crumbs.
$t = new Smarty();
$t->assign('name', $title);

$crumbs = array();
$crumbs[] = array(
	'name' => "Sumar",
	'href' => "?cid={$cid}&id={$person->id}"
);

if ($exp = isExpand($_GET['exp'])) {
  $crumbs[] = array(
    'name' => $person->getLongTitleForWhat($exp),
    'href' => ''
  );
}

$t->assign('crumbs', $crumbs);
$t->display('person_top_bar.tpl');


// ------------ This person's left hand side identity, photo and links
echo "<table width=970>".
     "<td valign=top width=340>";

echo "<div class=identity>";
$img = $person->getFact('image');
if (is_file("images/people/{$person->id}.jpg")) {
  $fname = "images/people/{$person->id}.jpg";
  $count = 1;
  // Get the most recent file we have for this person.
  while (is_file($fname)) {
    $img = $fname;
    $fname = "images/people/{$person->id}_$count.jpg";
    $count++;
  }
}
if (!$img) { $img = 'images/face2.jpg'; }

list($width, $height, $type, $attr) = getimagesize($img);

$width = min(250, $width);
$t = $width == 250 ? "width=$width" : "";

echo "<div class=identity_img><img src=\"$img\" $t></div>";

?>

<div class="fb-like" style="margin-top: 15px;margin-bottom:15px;"
     data-href="http://hartapoliticii.ro/?name=<? echo $person->getUrlName() ?>"
     data-send="false" data-width="330" data-show-faces="true"
     data-action="like" data-font="verdana"></div>

<!-- Place this tag where you want the +1 button to render -->
<g:plusone annotation="inline" width="330" href="http://hartapoliticii.ro/?name=<? echo $person->getUrlName() ?>"></g:plusone>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  window.___gcfg = {lang: 'en'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<?

// ----------------------------------------------------------
// -------------- The left hand side section ----------------
// Display the most recent news stuff.

$t = new Smarty();
$t->assign('qualifiers', $person->getNewsQualifiers(10));
$t->display('person_qualifiers.tpl');

// TODO: make the compact mods also something automatic.
include('mods/news/mod_compact.php');

$t = new Smarty();
$t->assign('title', $title);
$t->assign('idperson', $person->id);
$t->display('person_sidebar_extra_links.tpl');

echo "</div></td>";

// ----------------------------------------------------------
// --------------- The main mods section --------------------
echo "<td valign=top>";

if ($exp = isExpand($_GET['exp'])) {
  $moduleTitle = $person->getLongTitleForWhat($exp);

  echo "<div class=module>";
  // Print the title of the mod.

  echo "<div class=moduletitle>$moduleTitle</div>";
  echo "<div class=modulecontent>";

  // Inclue the expanded mod so it can do whatever
  include('mods/' . $exp . '/mod_expanded.php');
  echo "</div></div>";
} else {
	// ------------ This person's right hand side identity, the history modules
	foreach ($history as $item) {
	  echo "<div class=module>";

	  $moduleTitle = $person->getLongTitleForWhat($item);
	  echo "<div class=moduletitle>$moduleTitle</div><div class=modulecontent>";

	  if (file_exists("mods/$item/mod.php")) {
	    include("mods/$item/mod.php");
	  } else if (file_exists("mods/$item/mod_compact.php")) {
      include("mods/$item/mod_compact.php");
	  }
	  echo "</div></div>";
	}
}
?>
<?php
include('person_comments.php')
?>
<?php
echo "</td></table>";
?>
