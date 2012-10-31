<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: http://hartapoliticii.ro');

include_once('hp-includes/electoral_colleges.php');
include_once('hp-includes/string_utils.php');

/**
 * Extracts the county short name from a give full college name. So for example
 * from "D3 Arges" this will extract "AG". Unfortunately, I think the best
 * way to do this is with a giant switch statement.
 */
function getCollegeCountyShort($college_name) {
  $name = strtolower_ro($college_name);
  $county_hash = array(
    "alba" => "AB",
    "arad" => "AR",
    "arges" => "AG",
    "bacau" => "BC",
    "bihor" => "BH",
    "bistrita-nasaud" => "BN",
    "botosani" => "BT",
    "brasov" => "BV",
    "braila" => "BR",
    "buzau" => "BZ",
    "calarasi" => "CL",
    "caras-severin" => "CS",
    "cluj" => "CJ",
    "constanta" => "CT",
    "covasna" => "CV",
    "dambovita" => "DB",
    "dolj" => "DJ",
    "galati" => "GL",
    "giurgiu" => "GR",
    "gorj" => "GJ",
    "hargita" => "HR",
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

$college_name = mysql_real_escape_string(ucwords($_GET['colegiul']));

$title = "Colegiul electoral " . $college_name;
include('header.php');

$t = new Smarty();

$t->assign("college_name", $college_name);

$t->assign("pc_county_short", getCollegeCountyShort($college_name));

preg_match("/(d|s)(\\d+) (\\w+)/", strtolower_ro($college_name), $matches);
$t->assign("pc_number", $matches[2]);
$t->assign("pc_id", $matches[1] == 'd' ? 15 : 14);

$t->assign("candidates_2008", getResults2008ForCollege($college_name));
$t->assign("id_winner_2008", getWinner2008ForCollege($college_name));
$t->assign("show_minorities_link", strpos($college_name, "D") === 0);

$t->assign("description_2008", getDescription2008ForCollege($college_name));

$t->display("electoral_college.tpl");

?>