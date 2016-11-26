<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 1000000;
$FLAG_CAN_CHANGE_DB = true;



function deleteAllContentFirst() {
  mysql_query("DELETE FROM results_2016 WHERE 1");
  mysql_query("DELETE FROM people_history WHERE what='results/2016'");
}


function getPartyId($p) {
  if ($p == 'PDL') $p = "PD-L";
  if ($p == 'Forta Civica') $p = "FC";
  if ($p == 'Miscarea Verzilor') $p = "MV";
  if ($p == 'Partidul Verde') $p = "PVE";
  if ($p == 'Alianta Socialista') $p = "PAS";
  if ($p == 'PPDD') $p = "PP_DD";
  if ($p == 'UNIUNEA DEMOCRATĂ MAGHIARĂ DIN ROMÂNIA (UDMR)') $p = "UDMR";
  if ($p == 'PARTIDUL NAȚIONAL LIBERAL (PNL)') $p = "PNL";
  if ($p == 'PARTIDUL SOCIAL DEMOCRAT (PSD)') $p = "PSD";
  if ($p == 'PARTIDUL ALIANȚA LIBERALILOR ȘI DEMOCRAȚILOR (ALDE)') $p = "ALDE";
  if ($p == 'PARTIDUL UNIUNEA SALVAȚI ROMÂNIA (PUSR)') $p = "PUSR";
  if ($p == 'PARTIDUL ROMÂNIA UNITĂ (PRU)') $p = "PRU";
  if ($p == 'PARTIDUL MIȘCAREA POPULARĂ (PMP)') $p = "PMP";
  if ($p == 'PARTIDUL PUTERII UMANISTE (SOCIAL-LIBERAL) (PPU)') $p = "PPU";
  if ($p == 'PARTIDUL SOCIALIST ROMÂN (PSR)') $p = "PSR";
  if ($p == 'PARTIDUL ROMÂNIA MARE (PRM)') $p = "PRM";
  if ($p == 'PARTIDUL ECOLOGIST ROMÂN (PER )') $p = "PER";
  if ($p == 'ALIANȚA NOASTRĂ ROMÂNIA (ANR)') $p = "ANR";
  if ($p == 'UNIUNEA CULTURALĂ A RUTENILOR DIN ROMÂNIA (UCRR)') $p = "UCRR";
  if ($p == 'Alianţa electorală ALTERNATIVA NAŢIONALĂ 2016 (PFP+PSDM+PCDR+PC) (AN2016)') $p = "AN2016";
  if ($p == 'PARTIDUL ROMILOR DEMOCRAȚI - PRD (PRD)') $p = "PRD";
  if ($p == 'PARTIDUL NOUA ROMÂNIE (PNR)') $p = "PNR";
  if ($p == 'PARTIDUL ”BLOCUL UNITĂȚII NAȚIONALE” (BUN)') $p = "BUN";
  if ($p == 'UNIUNEA ARMENILOR DIN ROMÂNIA (URR)') $p = "URR";
  if ($p == 'COMUNITATEA RUȘILOR LIPOVENI DIN ROMÂNIA ((CRLR))') $p = "CRLR";
  if ($p == 'ASOCIAȚIA PARTIDA ROMILOR “PRO-EUROPA” (Pro Europa)') $p = "PRP";
  if ($p == 'FEDERAȚIA COMUNITĂȚILOR EVREIEȘTI DIN ROMÂNIA (FCER)') $p = "FCER";
  if ($p == 'PARTIDUL VERDE (VERZII)') $p = "PVE";
  if ($p == 'UNIUNEA DEMOCRATĂ TURCĂ DIN ROMÂNIA (UDTR)') $p = "UDTR";
  if ($p == 'UNIUNEA BULGARĂ DIN BANAT - ROMÂNIA (UBBR)') $p = "UBBR";
  if ($p == 'ASOCIAȚIA LIGA ALBANEZILOR DIN ROMÂNIA (ALAR)') $p = "ALAR";
  if ($p == 'ASOCIAȚIA MACEDONENILOR DIN ROMÂNIA (AMR)') $p = "AMR";
  if ($p == 'PARTIDUL PLATFORMA ACȚIUNEA CIVICĂ A TINERILOR (PACT)') $p = "PACT";
  if ($p == 'UNIUNEA SÂRBILOR DIN ROMÂNIA (USR)') $p = "USR";
  if ($p == 'UNIUNEA DEMOCRATICĂ A SLOVACILOR ȘI CEHILOR DIN ROMÂNIA (UDSCR)') $p = "UDSCR";
  if ($p == 'UNIUNEA CROAȚILOR DIN ROMÂNIA (UCR )') $p = "UCR";
  if ($p == 'UNIUNEA ELENĂ DIN ROMÂNIA (UER)') $p = "UER";
  if ($p == 'FORUMUL DEMOCRAT AL GERMANILOR DIN ROMÂNIA (FDGR)') $p = "FDGR";
  if ($p == 'UNIUNEA POLONEZILOR DIN ROMÂNIA (DOM POLSKI)') $p = "UPR";
  if ($p == 'ASOCIAȚIA ITALIENILOR DIN ROMÂNIA – RO.AS.IT. (ROASIT)') $p = "AIR";
  if ($p == 'PARTIDUL VRANCEA NOASTRĂ (PVN)') $p = "PVN";
  if ($p == 'UNIUNEA UCRAINENILOR DIN ROMÂNIA (UUR)') $p = "UUR";
  if ($p == 'PARTIDUL REPUBLICAN DIN ROMÂNIA (PRR)') $p = "PRR";
  if ($p == 'UNIUNEA SALVAȚI ROMÂNIA (USR)') $p = "PUSR";

  $s = mysql_query("SELECT id FROM parties WHERE name='{$p}'");
  $r = mysql_fetch_array($s);
  return $r['id'];
}


function addCandidateToCollege($college, $candidate, $candidateId, $party, $source) {
  if ($candidate == "") return;

  $context = "+ {{$college}} ({$party}) {$candidate}";
  info($context);

  $results = getPersonsByName($candidate, $context, infoFunction);

  if (count($results) == 0) {
    $person = addPersonToDatabase($candidate, $candidate);
  } else {
    $person = $results[0];
    info("  - person {{$candidate}} id : [" . $person->id . "]");
  }

  $partyId = getPartyId($party);
  if ($partyId <= 0) {
    die("party id gone wrong {$party} {$partyId}");
  }
  // Now that I have the person, let's populate the database with it.
  // We need two things: One an entry in results_2016, and an entry in
  // people_history so we can display the mod on their own page.

  mysql_query("
      INSERT INTO results_2016(nume, idperson, idcandidat, partid, idpartid, colegiu)
      values('{$candidate}', {$person->id}, {$candidateId}, '{$party}', $partyId, '{$college}')
  ");

  mysql_query("
      INSERT INTO people_history(idperson, what, url, time)
      values({$person->id}, 'results/2016', '{$source}', 1478450836)
  ");
}


function importFile($file_name) {
  global $startWith;

  $data = file_get_contents($file_name);
  $json = json_decode($data, true);

  info("[---------------- starting with {$startWith} ----------------]");

  for ($i = $startWith; $i < count($json); $i++) {
    $candidate = $json[$i];
    if ($candidate["EXPLICATIE"]) continue;
    if ($candidate["denumireformatiune"] == "") continue;

    //  {
    //    "id": 1,
    //    "nume": "SZEKERES",
    //    "prenume": "ILDIKÓ",
    //    "jud_dom": 1,
    //    "juddom": "ALBA",
    //    "loc_dom": 5309,
    //    "locdom": "LUNCA MUREŞULUI",
    //    "ocupatia": "PROFESOR",
    //    "profesia": "PROFESOR",
    //    "nrcirc": 41,
    //    "dencirc": "VRANCEA",
    //    "tip_camera": "S",
    //    "tip_formatiune": "M",
    //    "denumire_formatiune": 58,
    //    "denumireformatiune": "UNIUNEA DEMOCRATĂ MAGHIARĂ DIN ROMÂNIA (UDMR)"
    //  },

    $cam = $candidate["tip_camera"];
    if ($cam == 'CD') $cam = 'D';

    // remove diacritics
    $dencirc = getStringWithoutDiacritics($candidate["dencirc"]);
    if ($dencirc == "MUNICIPIUL BUCURESTI") $dencirc = "BUCURESTI";

    $college_name = ucwords(strtolower("{$cam} {$dencirc}"));

    addCandidateToCollege($college_name, "{$candidate["prenume"]} {$candidate["nume"]}", $candidate["pozitie"],
        $candidate["denumireformatiune"], "http://parlamentare2016.bec.ro/wp-content/uploads/2016/11/candidati.xlsx");

    $startWith = $i;
  }
}


function infoFunction($person, $idString) {
  return $person->name . ' ' . $idString;
}


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php

deleteAllContentFirst();

$startWith = (int)$_GET['startWith'];
importFile('candidates_2016.json');

include("../_bottom.php");
?>
</pre>
</body>
</html>
