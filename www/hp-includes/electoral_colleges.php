<?php

include_once('pages/functions_common.php');
include_once('hp-includes/string_utils.php');


$COUNTY_LIST = array(
    "București",
    "Alba",
    "Arad",
    "Argeș",
    "Bacău",
    "Bihor",
    "Bistrița Năsăud",
    "Botoșani",
    "Brasov",
    "Brăila",
    "Buzău",
    "Călărași",
    "Caraș Severin",
    "Cluj",
    "Constanța",
    "Covasna",
    "Dâmbovița",
    "Dolj",
    "Galați",
    "Giurgiu",
    "Gorj",
    "Harghita",
    "Hunedoara",
    "Ialomița",
    "Iași",
    "Ilfov",
    "Maramureș",
    "Mehedinți",
    "Mureș",
    "Neamț",
    "Olt",
    "Prahova",
    "Satu Mare",
    "Salaj",
    "Sibiu",
    "Suceava",
    "Teleorman",
    "Timiș",
    "Tulcea",
    "Vaslui",
    "Valcea",
    "Vrancea",
    "Străinătate"
  );

/**
 * Returns the id of the winner of the 2008 elections.
 *
 * TODO(vivi): Refactor and generalize this.
 *
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 */
function getWinner2008ForCollege($college) {
  $sql =
    "SELECT college, idperson_winner, idperson_runnerup ".
    "FROM results_2008_agg ".
    "WHERE college = '{$college}'";

  $r = mysql_fetch_array(mysql_query($sql));
  return $r['idperson_winner'];
}


/**
 * Returns the list of candidates and their results for this college. For now
 * this method is not generic at all, hence the very specific name.
 *
 * TODO(vivi): Refactor and generalize this.
 *
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 */
function getResults2008ForCollege($college) {
  $sql =
    "SELECT people.id, people.display_name, people.name, res.voturi, ".
        "parties.name AS party, cand.reason, cand.difference, ".
        "parties.minoritati ".
    "FROM results_2008 AS res ".
    "LEFT JOIN people ON people.id = res.idperson ".
    "LEFT JOIN parties ".
        "ON parties.id = res.idpartid ".
    "LEFT JOIN results_2008_candidates AS cand ".
        "ON cand.idperson = people.id ".
    "WHERE colegiu = '{$college}' ".
    "ORDER BY voturi DESC";

  $s = mysql_query($sql);

  $candidates = array();

  while ($r = mysql_fetch_array($s)) {
    $person_object = $r;
    $person_object['tiny_img_url'] = getTinyImgUrl($r['id']);
    $candidates[] = $person_object;
  }
  return $candidates;
}



/**
 * Returns the list of candidates and their results for this college. For now
 * this method is not generic at all, hence the very specific name.
 *
 * TODO(vivi): Refactor and generalize this.
 *
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 */
function getCollegeCandidates($college, $year) {
  $sql =
    "SELECT people.id, people.display_name, people.name, ".
        "parties.name AS party, history.url as source " .
    "FROM results_{$year} AS results ".
    "LEFT JOIN people ON people.id = results.idperson ".
    "LEFT JOIN parties ".
        "ON parties.id = results.idpartid ".
    "LEFT JOIN people_history AS history ".
        "ON people.id = history.idperson AND history.what = 'results/2012' ".
    "WHERE colegiu = '{$college}' " .
    "ORDER BY people.display_name ASC";

  $s = mysql_query($sql);

  $candidates = array();

  while ($r = mysql_fetch_array($s)) {
    $person = new Person();
    $person->name = $r['name'];
    $person->id = $r['id'];

    $person_object = $r;
    $person_object['tiny_img_url'] = getTinyImgUrl($r['id']);
    $person_object['history_snippet'] =
        $person->getHistorySnippet(array('results/2012'), true);

    // HACK to show the USL/ARD alliance for parties.
    $displayed_party_name = $r["party"];
    $logo = $r["party"];
    if ($year == "2012") {
      switch($r["party"]) {
        case "PSD": $displayed_party_name = "PSD (USL)"; $logo = "usl"; break;
        case "PNL": $displayed_party_name = "PNL (USL)"; $logo = "usl"; break;
        case "PC": $displayed_party_name = "PC (USL)"; $logo = "usl"; break;

        case "PD-L": $displayed_party_name = "PD-L (ARD)"; $logo = "ard"; break;
        case "FC": $displayed_party_name = "FC (ARD)"; $logo = "ard"; break;
        case "PNTCD": $displayed_party_name = "PNTCD (ARD)"; $logo = "ard"; break;
      }
    }
    $person_object["displayed_party_name"] = $displayed_party_name;
    $person_object["party_logo"] = $logo;

    $candidates[] = $person_object;
  }
  return $candidates;
}


function getCollegePeopleIds($college, $year) {
  $sql =
    "SELECT idperson " .
    "FROM results_{$year} ".
    "WHERE colegiu = '{$college}'";

  $s = mysql_query($sql);

  $candidates = array();

  while ($r = mysql_fetch_array($s)) {
    $candidates[] = $r['idperson'];
  }
  return $candidates;
}


/**
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 * @return
 */
function getDescription2008ForCollege($college) {
  $parts = explode(" ", $college);

  $sql =
    "SELECT description ".
    "FROM alegeri_2008_colleges ".
    "WHERE url LIKE '%{$parts[1]}-{$parts[0]}'";

  $r = mysql_fetch_array(mysql_query($sql));
  return $r['description'];
}


/**
 * @param {string} $college The college name needs to be in the form of
 *     "S1 Alba" or "D3 Prahova". Capitalization is important.
 * @return
 */
function getDescriptionsForCollege($college_name) {
  if (startsWith($college_name, 'D')) {
    $sql =
      "SELECT description AS d ".
      "FROM electoral_colleges ".
      "WHERE name_cdep = '{$college_name}'";
  } else {
    $sql =
      "SELECT name_cdep AS d ".
      "FROM electoral_colleges ".
      "WHERE name_senat='{$college_name}' ".
      "GROUP BY name_cdep";
  }

  $s = mysql_query($sql);
  $descriptions = array();
  while ($r = mysql_fetch_array($s)) {
    $descriptions[] = $r['d'];
  }
  return $descriptions;
}


function getDescriptionSourceForCollege($college_name) {
  if (startsWith($college_name, 'D')) {
    $sql =
      "SELECT distinct(source) ".
      "FROM electoral_colleges ".
      "WHERE name_cdep='{$college_name}'";
  } else {
    $sql =
      "SELECT distinct(source_senat) AS source ".
      "FROM electoral_colleges ".
      "WHERE name_senat='{$college_name}'";
  }

  $s = mysql_query($sql);

  $r = mysql_fetch_array($s);
  return $r['source'];
}


/**
 * Extracts the county short name from a give full college name. So for example
 * from "D3 Arges" this will extract "AG". Unfortunately, I think the best
 * way to do this is with a giant switch statement.
 */
function getCollegeCountyShort($college_name) {
  $name = getStringWithoutDiacritics(strtolower_ro($college_name));
  $county_hash = array(
    "alba" => "AB",
    "arad" => "AR",
    "arges" => "AG",
    "bacau" => "BC",
    "bihor" => "BH",
    "bistrita nasaud" => "BN",
    "bistrita-nasaud" => "BN",
    "botosani" => "BT",
    "brasov" => "BV",
    "braila" => "BR",
    "buzau" => "BZ",
    "calarasi" => "CL",
    "caras-severin" => "CS",
    "caras severin" => "CS",
    "cluj" => "CJ",
    "constanta" => "CT",
    "covasna" => "CV",
    "dambovita" => "DB",
    "dolj" => "DJ",
    "galati" => "GL",
    "giurgiu" => "GR",
    "gorj" => "GJ",
    "harghita" => "HR",
    "hunedoara" => "HD",
    "ialomita" => "IL",
    "iasi" => "IS",
    "ilfov" => "IF",
    "maramures" => "MM",
    "mehedinti" => "MH",
    "mures" => "MS",
    "neamt" => "NT",
    "olt" => "OT",
    "prahova" => "PH",
    "satu mare" => "SM",
    "salaj" => "SJ",
    "sibiu" => "SB",
    "suceava" => "SV",
    "teleorman" => "TR",
    "timis" => "TM",
    "tulcea" => "TL",
    "vaslui" => "VS",
    "valcea" => "VL",
    "vrancea" => "VN",
    "bucuresti" => "B"
  );

  preg_match("/(d|s)(\\d+) (.*)/", $name, $matches);
  return $county_hash[$matches[3]];
}


function getCollegeNumber($college_name) {
  preg_match("/(d|s)(\\d+) (\\w+)/", strtolower_ro($college_name), $matches);
  return $matches[2];
}


function getElectoralCollegeHashByCounty($chamber) {
  $hash = array();
  $s = mysql_query("
      SELECT * FROM electoral_colleges
      GROUP BY name_{$chamber}
  ");

  while ($r = mysql_fetch_array($s)) {
    $parts = explode(" ", $r["name_{$chamber}"], 2);
    $key = $parts[1];

    if (!array_key_exists($key, $hash)) $hash[$key] = array();

    $hash[$key][] = $parts[0];
  }

  return $hash;
}

?>