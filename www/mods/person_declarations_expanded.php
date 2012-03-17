<?php
include_once('hp-includes/declarations.php');

$highlightsPerDeclaration = array();

/**
 * Returns the array of highlights for this declaration. The separate ranges
 * will all be merged together and only continuous disjoint ranges will be
 * returned. So if the real highlights are like this:
 *    +-------+   +---------+     +-----+
 *       +----------+
 * What will be returned will look like this:
 *    +---------------------+     +-----+
 *
 * @param {Number} $declarationId
 * @param {Number} $uid
 * @return {Array}
 */
function getHighlightsForDeclaration($declarationId, $include=0, $exclude=0) {
  global $highlightsPerDeclaration;

  $declarationLink = getDeclarationLink($declarationId);

  if ($include != 0) {
    $sql = "
        SELECT *
        FROM people_declarations_highlights
        WHERE source='{$declarationLink}' AND user_id = {$include}";
  } else if ($exclude != 0) {
    $sql = "
        SELECT *
        FROM people_declarations_highlights
        WHERE source='{$declarationLink}' AND user_id != {$exclude}";
  } else {
    $sql = "
        SELECT *
        FROM people_declarations_highlights
        WHERE source='{$declarationLink}'";
  }

  //echo $sql;
  // Keep it simple and inefficient, that's okay.
  $map = array();

  $maxIndex = 0;
  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    for ($i = $r['start_word']; $i <= $r['end_word']; $i++) {
      $map[$i] = 1;
      $maxIndex = max($maxIndex, $i);
    }
  }

  $map[$maxIndex + 1] = 0;
  $state = 0;
  $start = 0;
  $ranges = array();

  for ($i = 0; $i < sizeof($map); $i++) {
    $map[$i] = $map[$i] ? $map[$i] : 0;
    if ($map[$i] != $state) {
      if ($state != 0) {
        $newRange = array(
          'start' => $start,
          'end' => $i - 1,
          'declarationId' => $declarationId
        );
        $ranges[] = $newRange;
        $highlightsPerDeclaration['declaration-' . $declarationId] = true;
      }
      $start = $i;
      $state = $map[$i];
    }
  }
  return $ranges;
}


/**
 * Returns an array of global ranges for this set of declarations.
 * @param $declarations
 * @return {Array}
 */
function getHighlights($declarations, $include=0, $exclude=0) {
  $ranges = array();
  foreach ($declarations as $declaration) {
    $ranges = array_merge($ranges,
        getHighlightsForDeclaration($declaration['id'], $include, $exclude));
  }
  return $ranges;
}


function constructUrl($baseUrl, $params, $newParams=array()) {
  $baseUrl .= '?';

  $p = array_merge($params, $newParams);

  foreach ($p as $key => $value) {
    $baseUrl .= "{$key}={$value}&";
  }

  return $baseUrl;
}


$title = "Declarații";

$pageSize = 10;
$start = (int)$_GET['start'];
$text_mode = $_GET['text_mode'] ? $_GET['text_mode'] : 'snippets';
$decl_type = $_GET['decl_type'] ? $_GET['decl_type'] : 'all';

// If this is set, it will only show this declaration. It's a navigational
// link.
$decl_id = (int)$_GET['decl_id'];

$dq = mysql_real_escape_string($_GET['dq'] ? $_GET['dq'] : '');

define('FULL_TEXT', true);

// NOTE(vivi): This code supports snippets too, but for now we are always going
// to display full text. We do that so that the marking of important passages
// gets a little easier to implement.
//
// NOTE(vivi): Take the type of declarations I am interested in into
// consideration.

$declarations = $person->searchDeclarations($dq, $start, $pageSize, FULL_TEXT,
                                            $decl_type, $decl_id);
$t = new Smarty();

$t->assign('id', $person->id);
$t->assign('name', $person->name);
$t->assign('dq', $dq);

$t->assign('logged_in', is_user_logged_in());

$t->assign('start', $start);

$t->assign('last_page', sizeof($declarations) < $pageSize);
$t->assign('first_page', $start == 0);

$t->assign('text_mode', $text_mode);
$t->assign('decl_type', $decl_type);
$t->assign('decl_id', $decl_id);

$currentParams = array(
  'name' => str_replace(' ', '+', $person->name),
  'exp' => 'person_declarations',
  'dq' => $dq,
  'start' => $start,
  'text_mode' => $text_mode,
  'decl_type' => $decl_type
);

$prevStart = $start - $pageSize;
$t->assign('prev_page_link', constructUrl('/', $currentParams,
                                          array('start' => $prevStart)));

$nextStart = $start + $pageSize;
$t->assign('next_page_link', constructUrl('/', $currentParams,
                                          array('start' => $nextStart)));


$t->assign('full_text_link', constructUrl('/', $currentParams));
$t->assign('snippets_link', constructUrl('/', $currentParams));

$t->assign('all_declarations_link', constructUrl('/', $currentParams, array(
  'decl_type' => 'all',
  'start' => 0
)));
$t->assign('important_declarations_link',
           constructUrl('/', $currentParams, array(
  'decl_type' => 'important',
  'start' => 0
)));
$t->assign('my_declarations_link', constructUrl('/', $currentParams, array(
  'decl_type' => 'mine',
  'start' => 0
)));

$ranges = getHighlights($declarations);
$myRanges = array();

if (is_user_logged_in()) {
  $ranges = getHighlights($declarations, 0, $uid);

  $uid = is_user_logged_in() ? $current_user->ID : 0;
  $myRanges = getHighlights($declarations, $uid, 0);
}

$newDeclarations = array();

foreach ($declarations as $declaration) {
  if ($decl_type == 'important' || $decl_type == 'mine') {
    $declaration['class'] = 'light_gray';
  } else {
    if ($highlightsPerDeclaration['declaration-' . $declaration['id']]) {
      $declaration['class'] = 'dark_gray';
    }
  }
  $declaration['link_to'] = constructUrl('/', array(), array(
    'name' => str_replace(' ', '+', $person->name),
    'exp' => 'person_declarations',
    'decl_id' => $declaration['id']
  ));
  array_push($newDeclarations, $declaration);
}
$t->assign('declarations', $newDeclarations);

$t->assign('highlights_global_ranges', $ranges);
$t->assign('highlights_my_ranges', $myRanges);

$t->display('mod_person_declarations_expanded.tpl');

?>
