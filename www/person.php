<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://www.hartapoliticii.ro');

include_once('hp-includes/wiki_edits.php');
include_once('hp-includes/people_lib.php');
include_once('hp-includes/people_util.php');

include_once('hp-includes/electoral_colleges.php');

include_once('hp-includes/follow_graph.php');
include_once('hp-includes/news.php');

// Checks that the string passed as a parameter represets and expanded module
// indeed. This method is for security reasons so that we don't load random
// files since we load the file based on the module name.
function isExpandedModule($str) {
  if ($str == 'news' || $str == 'cdep/2008' || $str == 'senat/2008' ||
      $str == 'person_declarations' || $str == 'gov/ro') {
    return $str;
  }
  return NULL;
}

$id = $_GET['id'];

// If the person is specified through a name, just search for it.
if ($_GET['name']) {
  $query = $_GET['name'];

  // HACK HACK HACK
  // The 'force' parameter is for politicalcolours.ro that used improper links
  // and was taking advantage of this old behaviour where any name would just
  // work as search. Because I broke that behaviour by restricting to exact
  // names, it broke their links.
  // This hack should be removed when nobody will link here anymore.
  if (isSet($_GET['force'])) {
    $persons = search($query);
    $id = $persons[0]->id;
  } else {
    $id = getPersonIdFromExactName(mysql_real_escape_string($query));
  }
}

// Given the ID of a person, go through it's history and load all
// the mods (modules) in the respective directories.
$person = new Person();
$person->setId($id);
$person->loadFromDb();
$img = $person->getImageUrl();

// The title of the page is the person's name, reversed (because in the db
// we keep the names as "LastName FirstName".
$title = $person->displayName;

?>
<!-- Add the following three tags inside head -->
<meta itemprop="name" content="<?php echo $person->displayName ?> - Harta Politicii din România">

<!-- the facebook OpenGraph Politician object -->
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# ro_hartapoliticii: http://ogp.me/ns/fb/ro_hartapoliticii#">
<meta property="fb:app_id" content="205183855930" />
<meta property="og:type"   content="ro_hartapoliticii:politician" />
<meta property="og:url"    content="http://hartapoliticii.ro/?name=<?php echo $person->getNameForUrl(); ?>" />
<meta property="og:title"  content="<?php echo $person->displayName ?>" />
<meta property="og:image"  content="http://hartapoliticii.ro/<?php echo $person->getMediumImageUrl(); ?>" />

<?php

$history = $person->getHistory();
if (in_array("results/2012", $history)) {
  $college = $person->get2012College();
  $title .= ", candidat {$college}";

  $party = $person->get2012Party();

  echo '<meta property="og:description" content="Candidat ' . $college . ', alegeri 2012" />';
  echo "\n";
  echo '<meta property="ro_hartapoliticii:party" content="Membru ' . $party . '" />';
}

// current_user is a variable set by Wordpress.
$uid = is_user_logged_in() ? $current_user->ID : 0;

include('header.php');

// ----------------------------------------------------------------
// -- Render the top bar, with the person name and bread crumbs. --
$t = new Smarty();
$t->assign('name', $person->displayName);

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
$t->assign('person_id', $person->id);
$t->display('person_top_bar.tpl');


// ----------------------------------------------------------------
// -- This person's left hand side identity, photo and links ------
echo "<table width=970 cellpadding=0 cellspacing=0>".
     "<td valign=top width=340>";
echo "<div class=identity>";

list($width, $height, $type, $attr) = getimagesize($img);

$width = min(250, $width);
$t = $width == 250 ? "width=$width" : "";

echo "<div class=identity_img><img src=\"$img\" $t></div>";

// ----------------------------------------------------------
// -------------- The left hand side section ----------------

$t = new Smarty();
$t->assign('following', $person->isFollowedByUserId($uid));
$t->assign('person_id', $person->id);
$t->assign('uid', $uid);
$t->assign('num_supporters', $person->getNumberOfSupporters());
$t->assign('supported_by_logged_in_user', $person->isSupportedBy($uid));
$t->assign('person_url', "http://hartapoliticii.ro/?name=" .
                         $person->getNameForUrl());
$t->display("person_sidebar_follow_button.tpl");

$t = new Smarty();
$t->assign('qualifiers', $person->getNewsQualifiers(10));
$t->display('person_qualifiers.tpl');

$college_name = $person->getActiveParliamentElectoralCollege();
if ($college_name) {
  $t = new Smarty();
  $t->assign('college_name', $college_name);

  $t->assign("pc_county_short", getCollegeCountyShort($college_name));
  $t->assign("pc_number", getCollegeNumber($college_name));
  $t->assign("pc_id", startsWith($college_name, "D") ? 15 : 14);

  $t->display('person_sidebar_electoral_college.tpl');
}


// Display all contact details of the person
include('mods/contact_details.php');

// TODO: make the compact mods also something automatic.
//include('mods/news_compact.php');

include('mods/person_declarations_compact.php');

$t = new Smarty();
$t->assign('name', $person->displayName);
$t->assign('idperson', $person->id);
$t->display('person_sidebar_extra_links.tpl');

echo "</div></td><td width=1></td>";

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
  } else {
    array_unshift($history, 'news/main');
  }

  // If we are simply displaying a person's page, go through all the modules
  // that we loaded from people_history and load them one by one.
  foreach ($history as $moduleName) {
    // Display the wrapper for the module, with a title.
    // TODO(vivi): Move this stuff into a template.
    if ($moduleName == "resume") {
      echo "<div class='module module_red'>";
    } else {
      echo "<div class='module'>";
    }
    $moduleTitle = $person->getLongTitleForWhat($moduleName);
    echo "<a name='{$moduleName}'>";
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
echo "<div class='module' style='padding:20px'>";
// The hook into WordPress comments, displayed for each person at the bottom.
include('person_comments.php');
echo "</div>";
?>

<?php
echo "</td></table>";
?>
