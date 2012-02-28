<?
// NOTE: This giant file is full of legacy code and old functions. It should be
// organized and cleaned up, a bunch of these functions should be moved to
// templates.

include('hp-includes/stats_utils.php');
include_once('hp-includes/user_utils.php');

/**
 * A warning that shows up on the 2004-2008 session pages where all we show is
 * that giant table with lots of data.
 * TODO: This should be moved into a template, and those pages should be
 * rewritten anyways.
 */
function printWarning() {
  ?>
  <div class=infotext>
    <b>Câteva explicații</b>: Aceste date sunt alcătuite din informațiile
    prezente pe <a href="http://www.cdep.ro">www.cdep.ro</a> și
    <a href="http://www.senat.ro">www.senat.ro</a>. Deși
    eu sper că sunt corecte, pentru că au fost alcătuite automat, este posibil
    să se fi strecurat greșeli.
    Dacă vedeți astfel de erori <a href="http://www.vivi.ro/blog">vă rog să mă
    contactați</a>, voi încerca să le repar. Cu toate astea, vă rog să țineți
    cont că pot exista erori neintenționate pentru care nu îmi asum
    răspunderea.

    <b>Atenție</b>: Există deputați al căror absenteism este oarecum motivat
    prin prezența în guvern și din păcate nu am lista lor și nu sunt marcați
    ca atare. Mai multe detalii despre cum și de ce am făcut site-ul ăsta,
    la secțiunea <a href="?cid=6">despre site</a> sau la mine
  <a href="http://www.vivi.ro/blog/?p=1015">pe blog</a>. Citește, e important.
  </div>
  <?
}


function getEuroParliamentString($id, $chamber) {
  $s = mysql_query(
    "SELECT * FROM away_times " .
    "WHERE away_times.iddepsen = $id AND chamber = $chamber AND ".
           "reason = 'EuroParlamentar'");
  if ($r = mysql_fetch_array($s)) {
    $left = $r['time_left'] / 1000;
    $back = $r['time_back'] / 1000;
    return $r['reason'] . ": " .
           date("M Y", $left) . " - " . date("M Y", $back);
  } else {
    return "";
  }
}


/**
 * Returns the sanitized sort order for the sql query for deputies
 * (and senators).
 */
function getSanitizedDeputiesSortBy($sortby) {
  if ($sortby != 'percent' && $sortby != 'seconds' && $sortby != 'idparty' &&
      $sortby != 'name') {
    $sortby = 'percent';
  }

  if ($order != 'desc' && $order != 'asc') {
    $order = 'asc';
    if ($sortby == 'percent') $order = 'desc';
    if ($sortby == 'name') $order = 'asc';
  }
  if ($sortby == 'idparty') {
    $sortby .= ' ' . $order . ', percent';
  }
  return $sortby . ' ' . $order;
}


/**
 * Shows a list of all the deputies, sorted by the percentage of presence
 * in votes.
 */
function showPresencePercentage($sortby, $order) {
  global $cid;
  $orderby = getSanitizedDeputiesSortBy($sortby, $order);

  $sql =
  "SELECT dep.id, dep.name, dep.idm, dep.timein, dep.timeout, dep.motif, ".
    "dep.idperson, ".
    "video.seconds, video.idv, video.sessions, ".
    "votes_agg.possible, votes_agg.percent, ".
    "belong_agg.idparty, ".
    "cand.url as dep_url, ".
    "colleges.url as col_url, ".
    "alegeritv.urlatv, " .
    "results.winner, results.difference, results.reason, " .
    "results_agg.total, results_agg.college " .
  "FROM cdep_2004_deputies AS dep " .

    "LEFT JOIN cdep_2004_video AS video ON video.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_votes_agg AS votes_agg ".
      "ON votes_agg.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_belong_agg AS belong_agg ".
      "ON belong_agg.idperson = dep.idperson " .

    "LEFT JOIN alegeri_2008_candidates AS cand " .
      "ON cand.idperson = dep.idperson " .
    "LEFT JOIN alegeri_2008_colleges as colleges ".
      "ON cand.college_id = colleges.id " .
    "LEFT JOIN alegeritv ON alegeritv.idperson = dep.idperson " .

    // TODO(vivi) Move results_2008 to idperson.
    "LEFT JOIN results_2008_candidates AS results ".
      "ON dep.idperson = results.idperson ".
    "LEFT JOIN results_2008_agg AS results_agg ".
      "ON results.college = results_agg.college ".
  "WHERE dep.timeout = 0 " .
  "ORDER BY $orderby";

  $sdep = mysql_query($sql);

  $numVotes = getNumberOfVotes();
  $count = 1;

  $stats = initStatsObject();

  echo "<table class=bigtable width=900>";
  $norder = $order == 'asc' ? 'desc' : 'asc';
  ?>
  <tr class=header><td></td><td>
    <a href=?cid=<?
    echo $cid ?>&sort=name&order=<?
    echo $norder?>>Deputat</a></td>
  <td>
    <a href=?cid=<?
    echo $cid ?>&sort=percent&order=<?
    echo $norder?>>Procent de voturi<br>la care a fost prezent</a></td>
  <td>
    <a href=?cid=<?
    echo $cid ?>&sort=idparty&order=<?
    echo $norder?>>Partid</a></td>
  <td>
    <a href=?cid=<?
    echo $cid ?>&sort=seconds&order=<?
    echo $norder?>>Luări de cuvânt</a></td>
  <td>Reales</td></tr>
  <?
  while ($rdep = mysql_fetch_array($sdep)) {
    // ------------------ vote percentages
    $timein = $rdep['timein'] / 1000;
    $timeout = $rdep['timeout'] / 1000;
    ?>
    <tr>
    <td align=right><? echo $count++ ?></td>
    <?
    $name = moveFirstNameLast($rdep['name']);
    ?>
    <td><a href="?name=<?
      urlencode($name) ?>&cid=9&id=<?
      echo $rdep['idperson'] ?>"><?echo $name ?></a>
    <br><span class="small gray">
    <?
    // ----------------- now print the times in office, if need be
    // 1103259600 = 17 dec 2004
    if ($timein != 1103259600 || $timeout != 0) {
      echo date("M Y", $timein) . " - " .
           ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
      echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";
    }
    echo getEuroParliamentString($rdep['id'], 2);
    echo "</span></td>";

    $sum = $rdep['possible'] * $rdep['percent'];
    $candidateVotes = $rdep['possible'];
    $percent = $rdep['percent'];

    $class = "blacktext";
    if ($percent < 0.5) $class = "red";
    if ($percent < 0.3) $class = "brightred";

    echo "<td><span class=$class>" .
         (floor(10000 * $percent) / 100) . " %</span>";
    if ($candidateVotes != $numVotes) {
      echo "<br><span class=\"small gray\"> din " .
           $candidateVotes . " voturi</span>";
      if ($awayVotes != 0) {
        echo "<br>Pentru $awayVotes a fost europarlamentar";
      }
    }
    echo "</td><td>";

    $parties = getPartiesFor($rdep['id'], 2004);
    echo getPartyName($parties[0]['name']);

    if (sizeof($parties) > 1) {
      echo '<br><span class="gray small">';
      for ($i = 1; $i < sizeof($parties); $i++) {
        echo "<b>" . getPartyName($parties[$i]['name']) .
             "</b> până în " . date("M Y", $parties[$i]['t'] / 1000);
        if ($i != sizeof($parties) - 1) {
          echo "<br>";
        }
      }
      echo '</span>';
    }
    echo "</td>";

    echo "<td>" . getVideoCellText($rdep['idv'], $rdep['sessions'],
                                   $rdep['seconds']) . "</td>";
    echo "<td>";

    echo getReElectedString($rdep);
    $stats = countStats($stats, $rdep, $percent);

    echo "</tr>";
  }
  echo "</table>";
  printStats($stats);
}


/**
 * Returns the nice string with why a deputy/senator was or was not reelected,
 * where, by how many votes and a reason.
 */
function getReElectedString($rdep) {
  $str = '';
  if ($rdep['col_url']) {
    if ($rdep['reason'] == '') {
      $str .= getLinkFromThinkopolisUrl($rdep['col_url'],
                                        $rdep['urlatv']) . "</td>";
    } else {
      if ($rdep['winner'] == 1) {
        $str .= '<span class=brightgreen>da</span> ';
        $str .= getCollegeNameFromThinkopolisUrl($rdep['col_url']);

      } else if ($rdep['winner'] == 0) {
        $str .= '<span class=brightred>nu</span> ';
        $str .= getCollegeNameFromThinkopolisUrl($rdep['col_url']);
      }
      $str .= "<span class=\"small gray\"> - cu </span>". $rdep['difference'];
      $str .= "<span class=\"small gray\"> voturi, ";
      if ($rdep['winner'] == 1) {
        $str .=  "următorul " . $rdep['reason'];
      } else {
        $str .=  $rdep['reason'];
      }
      $str .= "</span>";
    }
  }
  return $str;
}


// ------------------------------------------- Senate ------------------------

/**
 * Shows a list of all the deputies, sorted by the percentage of presence
 * in votes.
 */
function showSenatePresencePercentage($sortby, $order) {
  global $cid;

  if ($sortby != 'percent' && $sortby != 'seconds' &&
      $sortby != 'idparty' && $sortby != 'name') {
    $sortby = 'percent';
  }

  if ($order != 'desc' && $order != 'asc') {
    $order = 'asc';
    if ($sortby == 'percent') $order = 'desc';
    if ($sortby == 'name') $order = 'asc';
  }
  if ($sortby == 'idparty') {
    $sortby .= ' ' . $order . ', percent';
  }

  $sql =
    "SELECT sen.id, sen.name, sen.name_diacritics, sen.idm, ".
      "sen.timein, sen.timeout, sen.motif, sen.idperson, ".
      "votes.possible, votes.percent, ".
      "belong.idparty, ".
      "cand.url as dep_url, ".
      "colleges.url as col_url, " .
      "res.winner, res.difference, res.reason, " .
      "agg.total, agg.college " .
    "FROM senat_2004_senators AS sen " .
      "LEFT JOIN senat_2004_votes_agg AS votes ".
        "ON votes.idperson = sen.idperson " .
      "LEFT JOIN senat_2004_belong_agg AS belong ".
        "ON belong.idperson = sen.idperson " .
      "LEFT JOIN alegeri_2008_candidates AS cand " .
        "ON cand.idperson = sen.idperson " .
      "LEFT JOIN alegeri_2008_colleges as colleges ".
        "ON cand.college_id = colleges.id " .
      "LEFT JOIN results_2008_candidates AS res on cand.name = res.nume ".
      "LEFT JOIN results_2008_agg AS agg ON res.college = agg.college ".
    "WHERE timeout = 0 " .
    "ORDER BY $sortby $order";

  $sdep = mysql_query($sql);

  $numVotes = getSenatorsNumberOfVotes();
  $count = 1;

  $stats = initStatsObject();

  $norder = $order == 'asc' ? 'desc' : 'asc';

  ?>
  <table class=bigtable width=900>
  <tr class=header><td></td><td>
    <a href=?cid=<?
      echo $cid ?>&sort=name&order=<? echo $norder?>>Senator</a></td>
  <td>
    <a href=?cid=<?
      echo $cid ?>&sort=percent&order=<?
      echo $norder?>>Procent de voturi<br>la care a fost prezent</a>
      <br>începând cu Sep 2007</td>
  <td>
    <a href=?cid=<?
      echo $cid ?>&sort=idparty&order=<?
      echo $norder?>>Partid</a></td>
  <td>Reales</td></tr>
  <?
  while ($rdep = mysql_fetch_array($sdep)) {
    echo "<tr>";
    echo "<td align=right>" . ($count++) . ".</td>";
    echo "<td> " .
         "<a href=\"?cid=9&id=". $rdep['idperson'] . "\">" .
         $rdep['name_diacritics'] . "</a>";

    // ----------------- now print the times in office, if need be
    // ------------------ vote percentages
    $timein = $rdep['timein'] / 1000;
    $timeout = $rdep['timeout'] / 1000;

    echo "<br><span class=\"small gray\">";
    // 1076994000 = 17 dec 2004
    if ($timein != 1076994000 || $timeout != 0) {
      echo date("M Y", $timein) . " - " .
           ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
      echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";
    }
    echo getEuroParliamentString($rdep['id'], 1);
    echo "</span></td>";

    $sum = $rdep['possible'] * $rdep['percent'];

    $candidateVotes = $rdep['possible'];
    $percent = $rdep['percent'];

    $class = "blacktext";
    if ($percent < 0.5) $class = "red";
    if ($percent < 0.3) $class = "brightred";

    echo "<td><span class=$class>" .
        (floor(10000 * $percent) / 100) . " %</span>";
    if ($candidateVotes != $numVotes) {
      echo "<br><span class=\"small gray\"> din " .
          $candidateVotes . " voturi</span>";

      if ($awayVotes != 0) {
        echo "<br>Pentru $awayVotes a fost europarlamentar";
      }
      echo "</td>";
    }

    echo "<td>";
    $parties = getPartiesForSenator($rdep['id']);
    echo getPartyName($parties[0]['name']);

    if (sizeof($parties) > 1) {
      echo '<br><span class="gray small">';
      for ($i = 1; $i < sizeof($parties); $i++) {
        echo "<b>" . getPartyName($parties[$i]['name']) .
            "</b> până în " . date("M Y", $parties[$i]['t'] / 1000);
        if ($i != sizeof($parties) - 1) {
          echo "<br>";
        }
      }
      echo '</span>';
    }
    echo "</td>";

    echo "<td>" . getReElectedString($rdep) . "</td>";
    echo "</tr>";
    $stats = countStats($stats, $rdep, $percent);
  }
  echo "</table>";
  printStats($stats);
}


function getDeputyName($id) {
  $s = mysql_query("select name from cdep_2004_deputies where id = $id");
  $r = mysql_fetch_array($s);
  return ($r['name']);
}


function getSenatorName($id) {
  $s = mysql_query("select name from senat_2004_senators where id = $id");
  $r = mysql_fetch_array($s);
  return ($r['name']);
}

function getSenatorPersonId($id) {
  $s = mysql_query("select idperson from senat_2004_senators where id = $id");
  $r = mysql_fetch_array($s);
  return ($r['idperson']);
}

function getDeputyPersonId($id) {
  $s = mysql_query("select idperson from cdep_2004_deputies where id = $id");
  $r = mysql_fetch_array($s);
  return ($r['idperson']);
}

/**
 * Shows all information related to a deputy.
 * To be replaced with the more generic function to show a person.
 */
function showDeputy($id) {
  $sql =
  "SELECT dep.id, dep.name, dep.idm, dep.timein, dep.timeout, dep.motif, ".
    "video.seconds, video.idv, video.sessions, ".
    "votes_agg.possible, votes_agg.percent, ".
    "belong_agg.idparty, ".
    "cand.url as dep_url, ".
    "colleges.url as col_url, " .
    "dep.imgurl " .
  "FROM cdep_2004_deputies as dep " .
    "LEFT JOIN cdep_2004_video AS video ON video.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_votes_agg AS votes_agg ".
      "ON votes_agg.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_belong_agg AS belong_agg ".
      "ON belong_agg.idperson = dep.idperson " .
    "LEFT JOIN alegeri_2008_candidates AS cand ".
      "ON cand.idperson = dep.idperson " .
    "LEFT JOIN alegeri_2008_colleges as colleges ".
      "ON cand.college_id = colleges.id " .
  "WHERE dep.id = $id ";

  $sdep = mysql_query($sql);
  $rdep = mysql_fetch_array($sdep);

  $numVotes = getNumberOfVotes();

   // ------------------ vote percentages
   $timein = $rdep['timein'] / 1000;
   $timeout = $rdep['timeout'] / 1000;
   // 1103259600 = 17 dec 2004
   echo "<div class=numedeputattitlu>" . $rdep['name'] . "</div>";

   echo "<table cellspacing=10 cellpadding=10 width=800><td>";
   $imgurl = str_replace('imagini/l2004', 'parlamentari/l2004/mari',
                         $rdep['imgurl']);
   echo "<img src=http://www.cdep.ro" . $imgurl . " width=283><br>";
   echo "</td><td valign=top class=info>";

   // ----------------- now print the times in office, if need be
   echo "<br>În Camera Deputaților între: <b>";
   echo date("M Y", $timein) . " - " .
        ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
   if ($rdep['timeout'] != 0) {
     echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";
   }
   echo "</b>";

   $candidateVotes = $rdep['possible'];
   $percent = $rdep['percent'];

   $class = "blacktext";
   if ($percent < 0.5) {
     $class = "red";
   }
   if ($percent < 0.3) {
     $class = "brightred";
   }

   if ($timein < 1139201156) {
     echo "<br>Prezent la <b><span class=$class>" .
          (floor(10000 * $percent) / 100) . "%</span></b> din ";
     echo " voturile electronice dintre Februarie 2006 și ".
          "Noiembrie 2008 (3590).<br>";
   } else {
     echo "<br>Prezent la <b><span class=$class>" .
          (floor(10000 * $percent) / 100) . "%</span></b> din ";
     echo " voturile electronice dintre " . date("d M Y", $timein) .
          " și Noiembrie 2008 ($candidateVotes).<br>";
   }

   echo "<br>Luări de cuvânt: " .
        getVideoCellText2($rdep['idv'], $rdep['sessions'], $rdep['seconds']);

   echo "<br><br>Partid: ";
   $parties = getPartiesFor($rdep['id'], 2004);
   echo getPartyName($parties[0]['name']);

   if (sizeof($parties) > 1) {
     echo ' (<span class="gray small">';
     for ($i = 1; $i < sizeof($parties); $i++) {
       echo "<b>" . getPartyName($parties[$i]['name']) . "</b> până în " .
            date("M Y", $parties[$i]['t'] / 1000);
       if ($i != sizeof($parties) - 1) {
         echo ", ";
       }
     }
     echo '</span>)';
   }
   if ($rdep['dep_url']) {
     echo "<br><br>Candidează la: " .
          getLinkFromThinkopolisUrl($rdep['col_url'], "");
   } else {
     echo "<br>Nu pare să candideze în 2008.<br>";
   }

   echo "<br><br>Informează-te despre această persoană: ".
        "<ul class=moreinfolist>";
   echo "<li> <a href=\"http://www.cdep.ro/pls/parlam/structura.mp?".
        "idm=". $rdep['idm'] . "&cam=2&leg=2004\">site-ul cdep.ro</a></li> ";
   if ($rdep['dep_url']) {
     echo "<li> <a href=\"http://www.alegeri-2008.ro". $rdep['dep_url'] .
          "\">alegeri-2008.ro</a></li>";
   }
   echo "<li> <a href=\"http://www.google.ro/search?hl=ro&q=". $rdep['name'] .
        "&meta=lr%3Dlang_ro\">căutare google</a></li>";
   echo "<li> <a href=\"http://www.google.ro/search?hl=ro&q=". $rdep['name'] .
        "+site:wikipedia.org&&btnI=Mă Simt ".
        "Norocos&meta=lr%3Dlang_ro\">wikipedia</a></li>";
   echo "</ul>";

   echo "</td><tr><td colspan=2>";
   echo "</td></table>";
}

// ------------ show a senator ----------------------------

/**
 * Displays a senator from the 2004-2008 timeframe.
 *
 * TODO(vivi) Should be replaced by the generic displaying of a person,
 * starting from the person ID and figuring out all that is related to that
 * person, not starting from the fact that he was a senator (which does not
 * scale).
 */
function showSenator($id) {
  $sql =
    "SELECT sen.id, sen.name, idm, timein, timeout, motif, ".
      "possible, percent, idparty, cand.url as dep_url, ".
      "colleges.url as col_url, imgurl " .
    "FROM senat_2004_senators AS sen " .
      "LEFT JOIN senat_2004_votes_agg AS votes ".
        "ON votes.idperson = sen.idperson " .
      "LEFT JOIN senat_2004_belong_agg AS belong ".
        "ON belong.idperson = sen.idperson " .
      "LEFT JOIN alegeri_2008_candidates AS cand " .
        "ON cand.idperson = sen.idperson " .
      "LEFT JOIN alegeri_2008_colleges AS colleges ".
        "ON cand.college_id = colleges.id " .
    "WHERE sen.id = $id ";

  $sdep = mysql_query($sql);
  $numVotes = getSenatorsNumberOfVotes();

  $rdep = mysql_fetch_array($sdep);

   // ------------------ vote percentages
   $timein = $rdep['timein'] / 1000;
   $timeout = $rdep['timeout'] / 1000;
   // 1103259600 = 17 dec 2004

   echo "<div class=numedeputattitlu>" . $rdep['name'] . "</div>";

   echo "<table cellspacing=10 cellpadding=10 width=800><td>";
   $imgurl = str_replace('imagini', 'parlamentari',
                         $rdep['imgurl']);
   echo "<img src=http://www.cdep.ro" . $imgurl . " width=283><br>";
   echo "</td><td valign=top class=info>";

   // ----------------- now print the times in office, if need be
   echo "<br>În Senat între: <b>";
   echo date("M Y", $timein) . " - " .
        ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
   if ($rdep['timeout'] != 0) {
     echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";
   }
   echo "</b>";

   $candidateVotes = $rdep['possible'];
   $percent = $rdep['percent'];

   $class = "blacktext";
   if ($percent < 0.5) {
     $class = "red";
   }
   if ($percent < 0.3) {
     $class = "brightred";
   }

   if ($timein < 1188621956) {
     echo "<br>Prezent la <b><span class=$class>" .
          (floor(10000 * $percent) / 100) . "%</span></b> din ";
     echo " voturile electronice dintre Septembrie 2007 și " .
          "Noiembrie 2008 (750).<br>";
   } else {
  echo "<br>Prezent la <b><span class=$class>" .
       (floor(10000 * $percent) / 100) . "%</span></b> din ";
  echo " voturile electronice dintre " . date("d M Y", $timein) .
       " și Noiembrie 2008 ($candidateVotes).<br>";
  }
   echo "<br><br>Partid: ";
   $parties = getPartiesForSenator($rdep['id']);
   echo getPartyName($parties[0]['name']);

   if (sizeof($parties) > 1) {
     echo ' (<span class="gray small">';
     for ($i = 1; $i < sizeof($parties); $i++) {
       echo "<b>" . getPartyName($parties[$i]['name']) .
            "</b> până în " . date("M Y", $parties[$i]['t'] / 1000);
       if ($i != sizeof($parties) - 1) {
         echo ", ";
       }
     }
     echo '</span>)';
   }
   if ($rdep['dep_url']) {
     echo "<br><br>Candidează la: " .
          getLinkFromThinkopolisUrl($rdep['col_url'], '');
   } else {
     echo "<br>Nu pare să candideze în 2008.<br>";
   }

   echo "<br><br>Informează-te despre această persoană: ".
        "<ul class=moreinfolist>" .
        "<li> <a href=\"http://www.cdep.ro/pls/parlam/structura.mp?idm=".
        $rdep['idm'] . "&cam=1&leg=2004\">site-ul cdep.ro</a></li> ";
   if ($rdep['dep_url']) {
     echo "<li> <a href=\"http://www.alegeri-2008.ro".
          $rdep['dep_url'] . "\">alegeri-2008.ro</a></li>";
   }
   echo "<li> <a href=\"http://www.google.ro/search?hl=ro&q=" .
        $rdep['name'] . "&meta=lr%3Dlang_ro\">căutare google</a></li>";
   echo "<li> <a href=\"http://www.google.ro/search?hl=ro&q=" .
        $rdep['name'] . "+site:wikipedia.org&&btnI=Mă Simt ".
        "Norocos&meta=lr%3Dlang_ro\">wikipedia</a></li>";
   echo "</ul>";
   echo "</td><tr><td colspan=2>";
   echo "</td></table>";
}


// ----------------------------------------------- down to here

/**
 * From a thinkopolis URL returns a nice formatted link.
 */
function getLinkFromThinkopolisUrl($url, $atvurl) {
  if ($url == "") {
    return "";
  }
  $url = str_replace('Candidati-', 'Bucuresti-', $url);

  $parts = split('/', $url);
  $urlPart = str_replace('Candidati', 'Bucuresti',
                         $parts[sizeof($parts) - 1]);
  $str = $urlPart . "<br><span class=small>more: ";
  $str .= "<a href=$url class=\"gray\">thinkopolis</a>";

  $judetParts = split('-', $urlPart);
  $judet = $judetParts[0];
  $str .= ", <a  class=\"gray\" href=\"http://www.google.ro/search?hl=ro&q=".
          $judet . "+candidati+site:alegeri-2008.ro&&btnI=Mă Simt ".
          "Norocos&meta=lr%3Dlang_ro\">alegeri-2008.ro</a>";
  if ($atvurl != "") {
    $atvurl = str_replace("\"", "", $atvurl);
    $str .= ", <a href=\"$atvurl\" class=\"gray\">alegeri.tv</a>";
  }
  $str .= "</span>";
  //http://www.alegeri-2008.ro/candidati/bacau-4/
  return $str;
}

/**
 * From a thinkopolis URL returns a nice formatted link.
 */
function getCollegeNameFromUrl($url) {
  if ($url == "") {
    return "";
  }
  $url = str_replace('Candidati-', 'Bucuresti-', $url);

  $parts = split('/', $url);
  $urlPart = str_replace('Candidati', 'Bucuresti',
                         $parts[sizeof($parts) - 1]);
  return $urlPart;
}

function getAlegeriTvLink($nume) {
  return "<a href=\"http://www.google.ro/search?hl=ro&q=$nume+site%3Aalegeri.tv&btnI=M%C4%83+Simt+Norocos&meta=lr%3Dlang_ro&aq=f&oq=\">alegeri.tv</a>";
}


/**
 * From a thinkopolis URL returns a nice formatted link.
 */
function getElectionsLinks($url, $atvurl) {
  if ($url == "") {
    return "";
  }
  $url = str_replace('Candidati-', 'Bucuresti-', $url);

  $parts = split('/', $url);
  $urlPart = str_replace('Candidati', 'Bucuresti',
                         $parts[sizeof($parts) - 1]);

  $str = "<a href=$url class=\"gray\">thinkopolis</a>";

  $judetParts = split('-', $urlPart);
  $judet = $judetParts[0];

  if ($atvurl != "") {
    $atvurl = str_replace("\"", "", $atvurl);
    $str .= ", <a href=\"$atvurl\" class=\"gray\">alegeri.tv</a>";
  }
  return $str;
}


function getCollegeNameFromThinkopolisUrl($url) {
  if ($url == "") {
    return "";
  }
  $url = str_replace('Candidati-', 'Bucuresti-', $url);

  $parts = split('/', $url);
  $urlPart = str_replace('Candidati', 'Bucuresti',
                         $parts[sizeof($parts) - 1]);
  return $urlPart;
}


function getMoreInfoLink($url) {
  if ($url) {
    return " / <a href=http://www.alegeri-2008.ro$url>Info candidat</a>";
  } else {
    return "";
  }
}


/**
 * Counts the number of distincts votes.
 */
function getNumberOfVotes() {
  $s = mysql_query("select distinct(idv) from cdep_2004_votes");
  return mysql_num_rows($s);
}


/**
 * Counts the number of distincts votes.
 */
function getSenatorsNumberOfVotes() {
  $s = mysql_query("select distinct(idv) from senat_2004_votes");
  return mysql_num_rows($s);
}


/**
 * Return the number of votes for a candidate's time in office.
 */
function getNumberOfVotesBetween($start, $end) {
  // 1103259600 = 17 dec 2004
  if ($start == 1103259600 && $end == 0) {
  return -1;
  }

  $sql = "select distinct(idv) from cdep_2004_votes ".
         "where time > " . ($start * 1000);
  if ($end != 0) {
    $sql = $sql . " and time < " . ($end * 1000);
  }
  $s = mysql_query($sql);
  return mysql_num_rows($s);
}


/**
 * Return the number of votes for a candidate's time in office.
 */
function getAwayVotes($id, $chamber) {
  $s = mysql_query("select * from away_times " .
                   "where away_times.iddepsen = $id and chamber = $chamber");
  $sum = 0;
  while ($r = mysql_fetch_array($s)) {
    $left = $r['time_left'] / 1000;
    $back = $r['time_back'] / 1000;

    if ($chamber == 2) {
      $sum += getNumberOfVotesBetween($left, $back);
    } else if ($chamber == 1) {
      $sum += getSenatorNumberOfVotesBetween($left, $back);
    }
  }

  return $sum;
}


/**
 * Return the number of votes for a candidate's time in office.
 */
function getSenatorNumberOfVotesBetween($start, $end) {
  // 1103259600 = 17 dec 2004
  if ($start == 1076994000 && $end == 0) {
    return -1;
  }

  $sql = "select distinct(idv) from senat_2004_votes ".
         "where time > " . ($start * 1000);
  if ($end != 0) {
    $sql = $sql . " and time < " . ($end * 1000);
  }
  $s = mysql_query($sql);
  return mysql_num_rows($s);
}


/**
 * For a deputy, extract the parties he's been through. :-)
 */
function getPartiesFor($idperson, $year) {
  $sql =
  "SELECT distinct(idperson + idparty * 1000), idperson, idparty, " .
    "max(time) as t, name ".
  "FROM `cdep_{$year}_belong` ".
    "LEFT JOIN parties ON parties.id = cdep_{$year}_belong.idparty ".
  "WHERE idperson=" . $idperson . " " .
  "GROUP BY idperson,idparty ".
  "ORDER BY t DESC ";

  $s = mysql_query($sql);
  $parties = array();

  while ($r = mysql_fetch_array($s)) {
    array_push($parties, $r);
  }
  return $parties;
}


/**
 * For a deputy, extract the parties he's been through. :-)
 */
function getPartiesForSenator($idsen) {
  $sql =
  "SELECT distinct(idsen + idparty * 1000), idsen, idparty, ".
    "max(time) as t, name ".
  "FROM `senat_2004_belong` ".
    "LEFT JOIN parties ON parties.id = senat_2004_belong.idparty ".
  "WHERE idsen=" . $idsen . " group by idsen,idparty ".
  "ORDER BY t DESC ";

  $s = mysql_query($sql);
  $parties = array();

  while ($r = mysql_fetch_array($s)) {
    array_push($parties, $r);
  }
  return $parties;
}


function getPartyName($party) {
  return $party == '-' ? 'Indep.' : $party;
}

function getPartyNameForId($id) {
  global $parties;
  if ($id) {
    return $parties[$id]['name'];
  }
  return '';
}

function getShortPartyNameForId($id) {
  global $parties;
  if ($id) {
    return $parties[$id]['pname'];
  }
  return '';
}

function eliminatePartyNames($query) {
  global $parties;

  $parts = preg_split('/[\s-]+/', $query);
  $new = array();

  $partiesByName = array();
  foreach ($parties as $p => $v) {
    $partiesByName[strtolower($v['name'])] = $p;
  }

  foreach ($parts as $p) {
    if (!$partiesByName[strtolower($p)]) {
      $new[] = $p;
    }
  }
  return implode(' ', $new);
}

function getVideoCellText($idv, $sessions, $sec) {
  $h = floor($sec / 3600);
  $m = floor(($sec % 3600) / 60);
  $s = $sec % 60;

  return
    $h . "h " . $m . "m " . $s . "s<br><span class=\"small gray\">în " .
    "<a href=\"http://www.cdep.ro/pls/steno/steno.lista?idv=" .
    $idv . "&leg=2004&idl=1\">" .$sessions . " puncte</a></span>";
}


function getVideoCellText2($idv, $sessions, $sec) {
  $h = floor($sec / 3600);
  $m = floor(($sec % 3600) / 60);
  $s = $sec % 60;

  return
    $h . "h " . $m . "m " . $s . "s în " .
    "<a href=\"http://www.cdep.ro/pls/steno/steno.lista?idv=" .
    $idv . "&leg=2004&idl=1\">" .$sessions . " puncte</a>";
}


function printCsvDeputies() {
  echo "<pre>";
  $s = mysql_query("select * from deputies");
  while ($r = mysql_fetch_array($s)) {
    echo $r['id'] . ',' . $r['name'] . ',' . $r['idm'] . "\n";
  }
  echo "</pre>";
}

?>
