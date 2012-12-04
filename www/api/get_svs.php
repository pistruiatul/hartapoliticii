<?php

include ('../_top.php');
include ('../functions.php');
include ('../hp-includes/string_utils.php');
require_once('../wp-config.php');


$ignore_words = array("str", "strada", "ale", "aleea", "din", "bld",
                        "bulevardul", "nr", "numarul", "piata", "pta", "orasul",
                        "comuna", "satul", "sat");

// TODO: make this function better.
//
function getWordsFromQuery($q) {
  global $ignore_words;

  $q = getStringWithoutDiacritics($q);

  $words = array();

  preg_match_all("([a-zA-Z]+)", $q, $matches);
  foreach ($matches[0] as $word) {
    if (!in_array($word, $ignore_words)) {
      $words[] = $word;
    }
  }

  return $words;
}


/**
 * Returns a hash map with all the polling stations matching a query.
 *
 * @param $words
 * @return array
 */
function getPollingStationsFor($words) {
  if (count($words) == 0) return array();

  $likes = array();
  foreach ($words as $word) {
    $likes[] = "artera LIKE '%{$word}%'";
    $likes[] = "artera LIKE '{$word}%'";
    $likes[] = "artera LIKE '%{$word}'";
  }

  $sql = "
    SELECT *
    FROM sectii_vot_detalii
    WHERE " . implode(' OR ', $likes) . "";

  $matches = array();

  $s = mysql_query($sql);
  while ($r = mysql_fetch_array($s)) {
    $does_match = false;
    foreach ($words as $word) {
      if (preg_match("/(?:^|[ .,-]+){$word}(?:[ .,-]+|$)/i",
                     strtolower(getStringWithoutDiacritics($r['artera'])))) {
        $does_match = true;
      }
    }
    if ($does_match) {
      $matches[$r['nr_sv'] . '-' . $r['nr_cir']] = $r['artera'];
    }
  }
  return $matches;
}

$prefixes = array('str. ', 'strada ', 'cal. ', 'calea ', 'bld. ', 'bulevardul ',
    'bld ', 'str ', 'șos. ', 'șoseaua ', 'int. ', 'intrarea ', 'ale. ',
    'aleea ', 'pța. ', 'piața ');


function grayOutStuff($description) {
  global $prefixes;

  foreach ($prefixes as $prefix) {
    if (startsWith(strtolower_ro($description), $prefix)) {
      return "<span class=gray>" . ucwords($prefix) . "</span>" .
          substr($description, strlen($prefix));
    }
  }
  return $description;
}


$uid = is_user_logged_in() ? $current_user->ID : 0;

$E = trim(mysql_real_escape_string($_GET['e']));
$W = trim(mysql_real_escape_string($_GET['w']));
$N = trim(mysql_real_escape_string($_GET['n']));
$S = trim(mysql_real_escape_string($_GET['s']));
$Z = trim(mysql_real_escape_string($_GET['z']));
$sv = trim(mysql_real_escape_string($_GET['sv']));
$q = trim(mysql_real_escape_string($_GET['q']));
$words = getWordsFromQuery($q);


if ($Z > 10 && $E > 0 && $W > 0 && $N > 0 && $S > 0) {
	$sql = "
	  SELECT group_concat(concat(nr_sv, '-', nr_cir)) AS svs, nr_sv, nr_cir,
	      institutie, adresa, lat, lon
		FROM sectii_vot 
		WHERE lat BETWEEN $S AND $N AND lon BETWEEN $W AND $E
		GROUP BY institutie, adresa, lat, lon
  ";
	$s = mysql_query($sql);

  $matchingPollingStations = getPollingStationsFor($words);

	$output = array();
	while ($sv = mysql_fetch_array($s)) {
    if ($uid == 1) {
      $sv['can_edit'] = true;
    }

    $list = explode(",", $sv['svs']);
    $matches = array();

    foreach ($list as $pollingStation) {
      if (array_key_exists($pollingStation, $matchingPollingStations)) {
        $matches[] = $pollingStation;
      }
    }
    if (count($matches) > 0) {
      $sv['is_match'] = implode(",", $matches);
    }
		$output[] = $sv;
	}

	echo json_encode($output);

} else if (isset($sv)) {
	$data = explode("-", $sv);
	#echo $data[0];

	$sql = "
	  SELECT d.artera, v.institutie, v.adresa
	  FROM sectii_vot_detalii AS d
	  LEFT JOIN sectii_vot AS v ON v.nr_cir = d.nr_cir AND v.nr_sv = d.nr_sv
		WHERE d.nr_cir = $data[1] and d.nr_sv = $data[0]
		ORDER BY d.artera";

	$s = mysql_query($sql);

  $output = "<div class=sv_description>";
  $institution = "";
  $address = "";

	while ($sv = mysql_fetch_array($s)) {
    $description = highlightWords(correctDiacritics($sv['artera']), $words);

    $institution = $sv['institutie'];
    $address = $sv['adresa'];

    $description = grayOutStuff($description);
    $output .= "<div> " . $description . "</div>";

	}
	echo "<div class=polling_title code='{$data[0]}-{$data[1]}'>" .
       "Secția de votare {$data[0]}</div>".
       "<div class=institution>{$institution}</div>" .
       "<div class=address>{$address}</div>"
       . $output . "</div>";

}

include ('../_bottom.php');

?>
