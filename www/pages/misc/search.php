<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: /');


$query = trim($_GET['q']);
$query_orig = $query;

$title = $query;

include_once('header.php');
include_once('hp-includes/people_lib.php');
include_once('hp-includes/declarations.php');

// eliminate the party name from the query string.
$query = eliminatePartyNames($query);
// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

if ($query) {
  $persons = search($query);
}

if ($query != "") {
  // add it to the database
  mysql_query(
    "INSERT INTO log_searches(query, time, ip, num_results)
     VALUES('". mysql_real_escape_string($query) . "', " . time() . ",
     '" .$_SERVER['REMOTE_ADDR'] . "', " . count($persons) . ")");
  $ssid = mysql_insert_id();
}

$t = new Smarty();

$t->assign('query', $query);
$t->assign('ssid', $ssid);

$p = array();
for ($i = 0; $i < count($persons); $i++) {
  $person = array(
    'id' => $persons[$i]->id,
    'tiny_img_url' => $persons[$i]->getTinyImgUrl(),
    'display_name' => $persons[$i]->displayName,
    'party_name' => getPartyNameForId($persons[$i]->getFact('party')),
    'history_snippet' => $persons[$i]->getHistorySnippet()
  );
  $p[] = $person;
}

$t->assign('persons', $p);

if (trim($_GET['d']) == 'true') {
  $declarations = searchDeclarations($query);
  $t->assign('declarations', $declarations);
  $t->assign('searched_declarations', true);
} else {
  $t->assign('searched_declarations', false);
}

$t->display('pages_misc_search.tpl');
