<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 1000000;
$FLAG_CAN_CHANGE_DB = true;



function deleteAllContentFirst() {
  mysql_query("DELETE FROM people_facts WHERE attribute LIKE 'alegeri/2016/%'");
  mysql_query("DELETE FROM people_history WHERE what='alegeri/2016'");
}


function getPartyId($p) {
  $s = mysql_query("SELECT id FROM parties WHERE name='{$p}'");
  $r = mysql_fetch_array($s);
  return $r['id'];
}


function addDataToCandidate($name, $data) {
  info("<a href={$data['sursa']} target=_blank>sursa</a>");
  $results = getPersonsByName($name, $name, infoFunction);

  if (count($results) == 0) {
    info("  - can't find {$name}");
  } else {
    $person = $results[0];
    info("  - person {{$name}} id : [" . $person->id . "]");
  }

  // Now that I have the person, let's populate the database with it.
  // We need two things: One an entry in results_2016, and an entry in
  // people_history so we can display the mod on their own page.

  // Populate the alegeriparlamentare2016 table with the structured data for
  // each of the candidate. Pull from it on their page, but only insert in
  // history the stuff that matters.

  $foo = $data['activitate_parlamentara'];
  if (strpos($foo, "Nu a mai fost membru") === FALSE) {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/activitate_parlam', '{$foo}')");
  }
  $foo = $data['activitate_profesionala'];
  if (strpos($foo, "Nu există informații publice") === FALSE &&
      $foo != "În lucru" &&
      $foo != "") {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/activitate_profes', '{$foo}')");
  }
  $foo = $data['declaratie_avere'];
  if ($foo != "" &&
      $foo != "Informații inexistent") {
    $link = substr($foo, strlen("[Vezi aici]()") - 1,
        strlen($foo) - strlen(") - Acest link duce la alt site") - strlen("[Vezi aici]()"));
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/declaratie_avere', '{$link}')");
  }
  $foo = $data['declaratie_interese'];
  if ($foo != "") {
    $link = substr($foo, strlen("[Vezi aici]()") - 1,
        strlen($foo) - strlen(") - Acest link duce la alt site") - strlen("[Vezi aici]()"));
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/declaratie_intere', '{$link}')");
  }
  $foo = $data['istoric_politic'];
  if ($foo != "" && $foo != "Nu a mai fost membru al Parlamentului." &&
      $foo != "În lucru") {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/istoric_politic', '{$foo}')");
  }

  $foo = $data['controverse'];
  $controverse = $foo;
  if ($foo != "" &&
      strpos($foo, "Nu am gasit ") === FALSE &&
      strpos($foo, "Nu există informații publice") === FALSE &&
      strpos($foo, "Nu există date") === FALSE) {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/controverse', '{$foo}')");
  }
  $foo = $data['integritate'];
  $integritate = $foo;
  if ($foo != "Nu sunt cunoscute probleme de integritate.\n" &&
      $foo != "" &&
      strpos($foo, "Nu are probleme") === FALSE) {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/integritate', '{$foo}')");
  }
  $foo = $data['stat_de_drept'];
  $stat_drept = $foo;
  if (strpos($foo, "  * Nu există informații") === FALSE &&
      strpos($foo, "Nu are ini") === FALSE) {
    mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/stat_de_drept', '{$foo}')");
  }

  mysql_query("INSERT INTO people_facts(idperson, attribute, value)
        values('{$person->id}', 'alegeri/2016/sursa', '{$data['sursa']}')");


  if (($controverse != "" &&
      strpos($controverse, "Nu am gasit ") === FALSE &&
      strpos($controverse, "Nu există informații publice") === FALSE &&
      strpos($controverse, "Nu există date") === FALSE) ||

      ($integritate != "Nu sunt cunoscute probleme de integritate.\n" &&
      $integritate != "" &&
      strpos($integritate, "Nu are probleme") === FALSE) ||

      (strpos($stat_drept, "  * Nu există informații") === FALSE &&
      strpos($stat_drept, "Nu are ini") === FALSE)) {
    // We only insert this into history if it's bad for this candidate.
    mysql_query("INSERT INTO people_history(idperson, what, url, time)
        values({$person->id}, 'alegeri/2016', '{$data['sursa']}', 1478400836)");
  }

}


function importFile($file_name) {
  global $startWith;

  $data = file_get_contents($file_name);
  $json = json_decode($data, true);

  info("[---------------- starting with {$startWith} ----------------]");

  for ($i = $startWith; $i < count($json); $i++) {
    $candidate = $json[$i];

//    {
//      "activitate_parlamentara": "[Vezi aici sinteza activit\u0103\u021biiparlamentare](http://www.senat.ro/FisaSenator.aspx?ParlamentarID=fecbcc7a-fe69-423e-a2e9-d2830ae1287f) - Acest link duce la alt site",
//    "activitate_profesionala": "  * **Senator **- Parlamentul Romaniei, 2012-2016\n  * **Consilier local** **PNL **- Timi\u015foara, 2008 - 2012\n  * **Administrator **- SC HuroSupermold SRL, 2004 - 2012\n  * **Director General** - SC EL-SYS-MEC SA, Timi\u015foara, 2004-2012\n  * **Director General** - SC SuperPlast SA, Arad, 2003-2012\n  * **Administrator** - SC Telco SRL, 1991 - 2003\n  * **\u015eef sec\u0163ie** - IAEM Timi\u015foara, 1990 - 1991\n  * **Inginer proiectant** - IAEM Timi\u015foara, 1989-1990\n  * **Inginer tehnolog** - IAEM Timi\u015foara, 1983 - 1989\n",
//    "controverse": "  * 2014 - decide s\u0103 p\u0103r\u0103seasc\u0103 PNL \u015fi s\u0103 se inscrie in partidul condus de pre\u015fedintele Senatului, C\u0103lin Popescu T\u0103riceanu: Partidul Liberal Reformator. Ehegartner sus\u0163ine c\u0103 liberalismul nu mai exist\u0103: \u201eExist\u0103 cei vandu\u0163i lui Ponta \u015fi cei devora\u0163i de Blaga\"\n  * 2016 - demisioneaz\u0103 de la conducerea filialei ALDE Timi\u015f deoarece candidatura s\u0103 la Prim\u0103ria Timi\u015foara nu a fost acceptat\u0103 la nivel central, fiind favorizat universitarul Ilare Bordea\u0219u\n",
//    "declaratie_avere": "[Vezi aici](http://www.senat.ro/Declaratii/Senatori/2012/av_ehegartner_petru_03_06_2016.PDF) - Acest link duce la alt site",
//    "declaratie_interese": "[Vezi aici](http://www.senat.ro/Declaratii/Senatori/2012/in_ehegartner_petru_03_06_2016.PDF) - Acest link duce la alt site",
//    "integritate": "  * Petru Ehegartner a fost declarat in incompatibilitate de Agen\u021bia Na\u021bional\u0103 de Integritate intrucat a de\u021binut, simultan, func\u021bia de senator \u0219i urm\u0103toarele calit\u0103\u021bi: administrator la SC Huro Supermold SRL (19 decembrie 2012 -- 8 mai 2013); administrator la SC Superplast International SRL (24 mai -- 23 octombrie 2013); administrator SC Superplast SRL (24 mai -- 16 septembrie 2013).\n",
//    "istoric_politic": "  * **Partidul Na\u021bional Liberal (PNL)**, 1997-2014 \n    * Pre\u015fedinte PNL Timi\u015foara, 2010 - 2012\n    * Membru BPT Timi\u015f, 2009 - 2012\n    * Pre\u015fedinte PNL Timi\u015foara, 2000-2001\n  * **Partidul Liberal Reformator (PLR)**, 2014 - 2015 \n    * Pre\u015fedinte al filialei de Timi\u015f\n  * **Alian\u021ba Liberalilor \u0219i Democra\u021bilor (ALDE)**, 2015-prezent \n    * Pre\u015fedinte al ALDE Timi\u015f, 2015 - 2016\n",
//    "nume": "Petru EHEGARTNER",
//    "stat_de_drept": "  * \u00cen martie 2015, a votat impotriva cererii procurorilor anticorup\u021bie de ridicare a imunit\u0103\u021bii lui Daniel \u0218ova in vederea re\u021binerii \u0219i arest\u0103rii preventive. DNA a cerut ridicarea imunit\u0103\u021bii pentru c\u0103 Dan \u015eova, fost ministru al Transporturilor, a falsificat \u015fi distrus probe pentru a sc\u0103pa de dosarul in care este acuzat c\u0103 a incasat ilegal 3 milioane \u015fi jumatate de lei de la Complexele energetice Rovinari \u015fi Turceni.\n",
//    "studii": "  * **Absolvent **- Curs Securitate \u015fi Diploma\u0163ie - Institutul Diplomatic Roman, 2012\n  * **Doctorat** - Ingineria Distribu\u0163iei Energiei Electrice - Universitatea de Vest Timi\u015foara, 2011\n  * **Licen\u0163\u0103 **- Facultatea de Electrotehnic\u0103 - Universitatea Politehnic\u0103 Timi\u015foara, 1983\n",
//    "sursa": "http://www.alegeriparlamentare2016.ro/candidati/candidat_detail/id:5"
//}
//    ,
    addDataToCandidate($candidate["nume"], $candidate);

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
importFile('alegeriparlamentare2016.json');

include("../_bottom.php");
?>
</pre>
</body>
</html>
