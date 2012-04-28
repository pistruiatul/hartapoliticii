<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://www.hartapoliticii.ro');

include_once('hp-includes/wiki_edits.php');
include_once('hp-includes/people_lib.php');
include_once('hp-includes/people_util.php');

// Checks that the string passed as a parameter represets and expanded module
// indeed. This method is for security reasons so that we don't load random
// files since we load the file based on the module name.
function isExpandedModule($str) {
  if ($str == 'news' || $str == 'cdep/2008' || $str == 'senat/2008' ||
      $str == 'person_declarations') {
    return $str;
  }
  return NULL;
}

$id = $_GET['id'];

// If the person is specified through a name, just search for it.
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

// The title of the page is the person's name, reversed (because in the db
// we keep the names as "LastName FirstName".
$title = $person->displayName;

?>
<!-- For facebook sharing -->
<meta property='og:site_name' content='Harta Politicii' />
<meta property='og:website' content='http://hartapoliticii.ro' />
<meta property='og:title' content='<?php echo $person->displayName ?> - Harta politicii din România' />
<meta property='fb:app_id' content='205183855930' />
<!-- Update your html tag to include the itemscope and itemtype attributes -->
<html itemscope itemtype="http://schema.org/Person">
<!-- Add the following three tags inside head -->
<meta itemprop="name" content="<?php echo $person->displayName ?> - Harta Politicii din România">

<?php
include('header.php');

// ----------------------------------------------------------------
// -- Render the top bar, with the person name and bread crumbs. --
$t = new Smarty();
$t->assign('name', $title);

$crumbs = array();
$crumbs[] = array(
  'name' => "Sumar",
  'href' => "?cid={$cid}&id={$person->id}"
);

if ($exp = isExpandedModule($_GET['exp'])) {
  $crumbs[] = array(
    'name' => $person->getLongTitleForWhat($exp),
    'href' => ''
  );
}

$t->assign('crumbs', $crumbs);
$t->display('person_top_bar.tpl');


// ----------------------------------------------------------------
// -- This person's left hand side identity, photo and links ------
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

// Display the LIKE and the +1 buttons.
// TODO: Move these in a template?
?>
<div class="fb-like" style="margin-top: 15px;margin-bottom:15px;"
     data-href="http://hartapoliticii.ro/?name=<?php echo $person->getUrlName() ?>"
     data-send="false" data-width="330" data-show-faces="false"
     data-action="like" data-font="verdana"></div>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  window.___gcfg = {lang: 'en'};
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
  })();
</script>
<?php

// ----------------------------------------------------------
// -------------- The left hand side section ----------------

// Display the most recent news stuff.

$t = new Smarty();
$t->assign('qualifiers', $person->getNewsQualifiers(10));
$t->display('person_qualifiers.tpl');

// Display all contact details of the person
include('mods/contact_details.php');

// TODO: make the compact mods also something automatic.
include('mods/news_compact.php');

include('mods/person_declarations_compact.php');

$t = new Smarty();
$t->assign('title', $title);
$t->assign('idperson', $person->id);
$t->display('person_sidebar_extra_links.tpl');

echo "</div></td>";

// ----------------------------------------------------------
// --------------- The main mods section --------------------
echo "<td valign=top>";

// If I am displaying an expanded module, this will take the entire content
// part and we are no longer displaying all the modules of one person.
//
if ($expandedModule = isExpandedModule($_GET['exp'])) {
  // Get the long title of the module to display.
  $moduleTitle = $person->getLongTitleForWhat($expandedModule);

  // TODO(vivi): Move this stuff into templates too.
  echo "<div class=module>";
  echo "<div class=moduletitle>$moduleTitle</div>";
  echo "<div class=modulecontent>";

  // Based on the module name, load the 'module_expanded.php' file.
  $filename = str_replace("/", "_", $expandedModule);
  include('mods/' . $filename . '_expanded.php');

  echo "</div></div>";

} else {
  $history = $person->getHistory();

  // If we only have one module for this person, append the expanded news module
  // at the end so that the page doesn't look totally stupid.
  if (sizeof($history) <= 1) {
    array_push($history, 'news/expanded');
  }

  // If we are simply displaying a person's page, go through all the modules
  // that we loaded from people_history and load them one by one.
  foreach ($history as $moduleName) {
    // Display the wrapper for the module, with a title.
    // TODO(vivi): Move this stuff into a template.
    echo "<div class=module>";
    $moduleTitle = $person->getLongTitleForWhat($moduleName);
    echo "<div class=moduletitle>$moduleTitle</div><div class=modulecontent>";

    // Based on the module name, load the 'module_compact.php' file.
    $filename = str_replace("/", "_", $moduleName);

    // This test is a bit of a hack so that we can display the expaneded news
    // module on the person's page when the person has only one other module.
    if (strrpos($filename, 'expanded') === false) {
      // If this is not an expanded module, include the compact one.
      include("mods/{$filename}_compact.php");
    } else {
      // Otherwise just include the expanded module.
      include("mods/{$filename}.php");
    }

    echo "</div></div>";
  }

}
?>

<?php
// The hook into WordPress comments, displayed for each person at the bottom.
include('person_comments.php')
?>

<?php
echo "</td></table>";
?>
