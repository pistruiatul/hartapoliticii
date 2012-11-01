<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: /');

$query = trim($_GET['q']);
// Just take out the quotes.
$query = str_replace("\\'", "", $query);
$query = str_replace("\\\"", "", $query);
$query = str_replace(",", "", $query);
$query = str_replace(".", "", $query);

$restrict = trim($_GET['r']);
$query_orig = $query;

$title = $query;

include_once('header.php');
include_once('hp-includes/people_lib.php');
include_once('hp-includes/declarations.php');

function collegeResultCompare($a, $b) {
  if ($a['score'] == $b['score']) {
    return 0;
  }
  return ($a['score'] < $b['score']) ? 1 : -1;
}


function setMatchedWords($array, $description, $words) {
  $w = getArrayWithoutDiacritics($words);
  $d = strtolower(getStringWithoutDiacritics($description));

  foreach ($w as $word) {
    $word = strtolower($word);
    $pos = strpos($d, $word);

    if ($pos === false) {
      // string needle NOT found in haystack
    } else {
      // string needle found in haystack
      $array[$word] = $word;
    }
  }
  return $array;
}


function getCollegeSearch($query) {
  $words = explode(" ", $query);

  $likes = array();
  foreach($words as $word) {
    if (strlen($word) > 1 && $word != "str" && $word != "ale" &&
        $word != "din" && $word != "bld" && $word != "strada") {
      $likes[] = "description LIKE '%{$word}%'";
    }
  }

  $where = implode(" OR ", $likes);
  $s = mysql_query("
      SELECT *
      FROM electoral_colleges
      WHERE {$where}");

  $result = array();
  while ($r = mysql_fetch_array($s)) {
    $key = $r['name_cdep'];

    if (array_key_exists($key, $result)) {
      $result[$key]['description'][] =
          highlightWords(correctDiacritics($r['description']), $words);

      $result[$key]['matched_words'] = setMatchedWords(
        $result[$key]['matched_words'], $r['description'], $words);
      $result[$key]['score'] = count($result[$key]['matched_words']);

    } else {
      $result[$key] = array();
      $result[$key]['description'] = array();
      $result[$key]['description'][] =
          highlightWords(correctDiacritics($r['description']), $words);

      $result[$key]['name_cdep'] = $r['name_cdep'];
      $result[$key]['name_senat'] = $r['name_senat'];

      $result[$key]['id'] = $r['id'];

      $result[$key]['matched_words'] = array();

      $result[$key]['matched_words'] = setMatchedWords(
        $result[$key]['matched_words'], $r['description'], $words);
      $result[$key]['score'] = count($result[$key]['matched_words']);
    }
  }

  foreach ($result as $key => $value) {
    foreach ($words as $word) {
      $pos = strpos(strtolower($key), strtolower($word));

      if ($pos === false) {
        // string needle NOT found in haystack
      } else {
        // string needle found in haystack
        $result[$key]['score'] += 2;
      }
    }
  }

  usort($result, "collegeResultCompare");
  return array_slice($result, 0, 15);
}


// eliminate the party name from the query string.
$query = eliminatePartyNames($query);
// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

if ($query && $restrict != "cs") {
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
$show_people = (count($p) > 0);

$t->assign('persons', $p);

if (trim($_GET['d']) == 'true') {
  $declarations = searchDeclarations($query);
  $t->assign('declarations', $declarations);
  $t->assign('searched_declarations', true);

  $show_people = $show_people || count($declarations) > 0;
} else {
  $t->assign('searched_declarations', false);
}

$colleges = getCollegeSearch($query);
$t->assign('colleges', $colleges);
$show_people = $show_people || count($colleges) == 0;

if ($restrict == 'cs') $show_people = false;
$t->assign('show_people', $show_people);

$t->display('pages_misc_search.tpl');
