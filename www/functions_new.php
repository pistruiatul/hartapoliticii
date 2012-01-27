<?


function printWarning() {
  ?>
  <table width=800 cellpadding=10><tr><td>
  <b>Câteva explicații</b>: Aceste date sunt alcătuite din informațiile prezente pe <a href="http://www.cdep.ro">www.cdep.ro</a>. Deși 
  eu sper că sunt corecte, pentru că au fost alcătuite automat, este posibil să se fi strecurat greșeli.
  Dacă vedeți astfel de erori <a href="http://www.vivi.ro/blog">vă rog să mă contactați</a>, voi încerca să le repar. Cu 
  toate astea, vă rog să țineți cont că pot exista erori neintenționate pentru care nu îmi asum răspunderea.
  <br><br>
  <b>Atenție</b>: Există deputați al căror absenteism este oarecum motivat prin prezența în guvern și din
  păcate nu am lista lor și nu sunt marcați ca atare.
  <br><br>
  Mai multe detalii despre cum și de ce am făcut site-ul ăsta, <a href="http://www.vivi.ro/blog/?p=1015">aici</a>. Citește, e important.
  </td></tr></table>
  <?
}


function printDeputyWarning() {
  ?>
  <div class="warning noprint">
  Dacă ți se pare relevantă pagina asta și ești din colegiul electoral al acestui candidat, dă-o mai departe. <b>Trimite link-ul
  la prieteni</b>. O poți multiplica, o poți printa, dă-o părinților, prietenilor, ajută-i să voteze informat. <br><br>
  Nu știu dacă avem optiuni bune de vot, dar cel puțin pentru candidații aceștia știm ce au făcut timp de patru ani. 
  </div>
  <?
}

/**
 * Shows a list of all the deputies, sorted by the percentage of presence
 * in votes.
 */
function showPresencePercentage($sortby, $order) {
  if ($sortby != 'percent' && $sortby != 'seconds' && $sortby != 'idparty' &&
      $sortby != 'name') {
    $sortby = 'percent';
  }

  if ($order != 'desc' && $order != 'asc') {
    $order = 'asc';  
  if ($sortby == 'percent') {
      $order = 'desc';
  }
  if ($sortby == 'name') {
    $order = 'asc';
  }
  }
  if ($sortby == 'idparty') {
  $sortby .= ' ' . $order . ', percent';
  }

  $sql = "select deputies.id, deputies.name, idm, timein, timeout, motif, seconds, idv, ".
                "sessions, possible, percent, idparty, candidates.url as dep_url, colleges.url as col_url " .
         "from deputies " .
           "left join video on video.iddep = deputies.id " . 
           "left join votes_agg on votes_agg.iddep = deputies.id " .
           "left join belong_agg on belong_agg.iddep = deputies.id " .
           "left join candidates on candidates.name = replace(replace(deputies.name, '-', ' '), '  ', ' ') " .
           "left join colleges on candidates.college_id = colleges.id " .
         "where timeout = 0 " .
         "order by $sortby $order";
  $sdep = mysql_query($sql);

  $useAggregate = true;

  if (!$useAggregate) {
    mysql_query("delete from votes_agg where 1"); 
    mysql_query("delete from belong_agg where 1"); 
  }

  // the absolute number of total votes
  $absoluteTotalVotes = 0;
  $votesPerParty = array();
  $possibleVotesPerParty = array();

  $numVotes = getNumberOfVotes();
  $count = 1;
  echo "<table class=bigtable>";
  $norder = $order == 'asc' ? 'desc' : 'asc';
  echo "<tr class=header><td></td><td><a href=index.php?sort=name&order=$norder>Deputat</a></td>".
       "<td><a href=index.php?sort=percent&order=$norder>Procent de voturi<br>la care a fost prezent</a></td>" .
       "<td><a href=index.php?sort=idparty&order=$norder>Partid</a></td>".
       "<td><a href=index.php?sort=seconds&order=$norder>Luări de cuvânt</a></td>".
       "<td>Candidează</td>".
       "<td><a href=index.php?sort=seconds&order=$norder>Inițiative legislative</a><br>".
       "<font color=red style=font-size:0.8em>doar cele votate din Feb 2006</td>".
       "</tr>";
  while ($rdep = mysql_fetch_array($sdep)) {
    // ------------------ vote percentages 
    $timein = $rdep['timein'] / 1000;
    $timeout = $rdep['timeout'] / 1000;
    // 1103259600 = 17 dec 2004

    echo "<tr>";
    echo "<td align=right>" . ($count++) . ".</td>";
    echo "<td> " .
         "<a href=\"deputat.php?id=". $rdep['id'] . "\">" . $rdep['name'] . "</a>";

    // ----------------- now print the times in office, if need be
    echo "<br><span class=\"small gray\">";
    if ($timein != 1103259600 || $timeout != 0) {
      echo date("M Y", $timein) . " - " . ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
      echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";    
    }
    echo "</span>";
    echo "</td>";

    if (!$useAggregate) {
      $sql = 
      "select vote, count(*) as cnt " .
      "from votes " . 
      "where iddep = " . $rdep['id'] . " " .
      "group by vote";
      $s = mysql_query($sql);
    
      $vt = array();
      $vt['DA'] = 0;
      $vt['NU'] = 0;
      $vt['Abţinere'] = 0;
      $vt['-'] = 0; 
      while ($r = mysql_fetch_array($s)) {
      $vt[$r['vote']] = $r['cnt'];  
      }
    //echo $vt['DA'] . " / " . $vt['NU'] . " / ". $vt['Abţinere'] . " - " . $vt['-'];
      $sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-'];
    } else {
    $sum = $rdep['possible'] * $rdep['percent'];
    }

    $absoluteTotalVotes += $candidateVotes;

    if (!$useAggregate) {
      $candidateVotes = getNumberOfVotesBetween($timein, $timeout);
      $candidateVotes = $candidateVotes == -1 ? $numVotes : $candidateVotes;
 
      $percent = 1.0 * $sum / $candidateVotes; 
    } else {
    $candidateVotes = $rdep['possible'];
    $percent = $rdep['percent'];
    }
    $class = "blacktext";
    if ($percent < 0.5) {
    $class = "red";
    }
    if ($percent < 0.3) {
    $class = "brightred";
    }

    echo "<td><span class=$class>" . (floor(10000 * $percent) / 100) . " %</span>";
    if ($candidateVotes != $numVotes) {
    echo "<br><span class=\"small gray\"> din " . $candidateVotes . " voturi</span></td>";
  }
  
  echo "<td>";
    
    $parties = getPartiesFor($rdep['id']);
    echo getPartyName($parties[0]['name']);
    
    $possibleVotesPerParty[$parties[0]['name']] += $candidateVotes;
    $votesPerParty[$parties[0]['name']] += $sum;

    if (!$useAggregate) {    
      mysql_query("insert into belong_agg(iddep, idparty) values(" . $rdep['id'] . ", " . $parties[0]['idparty'] . ")");
    }
    if (sizeof($parties) > 1) {
      echo '<br><span class="gray small">';
      for ($i = 1; $i < sizeof($parties); $i++) {
      echo "<b>" . getPartyName($parties[$i]['name']) . "</b> până în " . date("M Y", $parties[$i]['t'] / 1000);
      if ($i != sizeof($parties) - 1) {
      echo "<br>";
      }
      }
      echo '</span>';
    }
    echo "</td>";  
    echo "<td>" . getVideoCellText($rdep['idv'], $rdep['sessions'], $rdep['seconds']) . "</td>";
    echo "<td>" . getLinkFromThinkopolisUrl($rdep['col_url']) . "</td>";
    echo "<td>" . getLawProponentStats($rdep['id']) . "</td>";
  echo "</tr>";

    // How about we make a big fat sql for the summary here.
    //$sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-']
    //$candidateVotes, $percent
    if (!$useAggregate) {
      $insertAgg = "insert into votes_agg(iddep, vda, vnu, vab, vmi, possible, percent) " .
          "values(" . $rdep['id'] . ", " . $vt['DA'] . ", " . $vt['NU'] . ", " . $vt['Abţinere'] . ", " . $vt['-'] .
                  ", " . $candidateVotes . ", " . $percent . ")";
      echo '<td>' . $insertAgg . '</td>';
      mysql_query($insertAgg);
    }
  }
  echo "</table>";
  /*
  echo $absoluteTotalVotes . "<br>";
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "PNL");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "PSD");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "PD-L");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "PC");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "UDMR");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "Minoritati");
  echo getVotesPerParty($votesPerParty, $possibleVotesPerParty, "-");
  */
}

function getLawProponentStats($depId) {
  $s = mysql_query(
  "select count(*) as cnt, status " .
    "from proponents " .
      "left join laws_status on laws_status.idlaw = proponents.idlaw " .
    "where proponents.iddep = $depId " .
    "group by laws_status.status");
  
  while ($r = mysql_fetch_array($s)) {
    switch ($r['status']) {
    case 1: $aprobate = $r['cnt']; break;
    case 2: $respinse = $r['cnt']; break;
      case 3: /* astea sunt legile încă pe rol */ break;
    }
  }
  $str = "<div class=\"small gray\">Aprobate $aprobate: ";
  $s = mysql_query(
  "select count(*) as cnt, authorscount " .
    "from proponents " .
      "left join laws_status on laws_status.idlaw = proponents.idlaw " .
    "where proponents.iddep = $depId and laws_status.status = 1 " .
    "group by authorscount");
  $score = 0;

  while ($r = mysql_fetch_array($s)) {
  $str .= $r['cnt'] . "/" . $r['authorscount'] . ", ";
  $score += $r['cnt'] / $r['authorscount'];
  }
  $str .= "</div><div class=\"small gray\">Respinse $respinse: ";
  
  $s = mysql_query(
  "select count(*) as cnt, authorscount " .
    "from proponents " .
      "left join laws_status on laws_status.idlaw = proponents.idlaw " .
    "where proponents.iddep = $depId and laws_status.status = 2 " .
    "group by authorscount");
  $score = 0;

  while ($r = mysql_fetch_array($s)) {
  $str .= $r['cnt'] . "/" . $r['authorscount'] . ", ";
  $score -= $r['cnt'] / $r['authorscount'];
  }
  $str .= "</div>";

  $str = "Scor: " . (floor(10 * $score) / 10) . " " . $str;
  return $str;
}

function getVotesPerParty($votes, $possible, $party) {
  return "Prezență " . $party . ": " . (floor(10000 * $votes[$party] / $possible[$party]) / 100) . "%<br>";

  return $party . " " . $votes[$party] . " / " . $possible[$party] .
       " avg presence: " . (floor(10000 * $votes[$party] / $possible[$party]) / 100) . "<br>";
}

function getDeputyName($id) {
  $s = mysql_query("select name from deputies where id = $id");
  $r = mysql_fetch_array($s);
  return ($r['name']);
}


function showDeputy($id) {
  $sql = "select deputies.id, deputies.name, idm, timein, timeout, motif, seconds, idv, ".
                "sessions, possible, percent, idparty, candidates.url as dep_url, colleges.url as col_url, " .
                "imgurl " .
         "from deputies " .
           "left join video on video.iddep = deputies.id " . 
           "left join votes_agg on votes_agg.iddep = deputies.id " .
           "left join belong_agg on belong_agg.iddep = deputies.id " .
           "left join candidates on candidates.name = replace(replace(deputies.name, '-', ' '), '  ', ' ') " .
           "left join colleges on candidates.college_id = colleges.id " .
         "where deputies.id = $id ";
  $sdep = mysql_query($sql);

  $numVotes = getNumberOfVotes();
  
  $rdep = mysql_fetch_array($sdep);

   // ------------------ vote percentages 
   $timein = $rdep['timein'] / 1000;
   $timeout = $rdep['timeout'] / 1000;
   // 1103259600 = 17 dec 2004
   echo "<div class=noprint><a href=index.php>&lt; Înapoi la prima pagină</a> | " .
        "<A HREF=\"javascript:window.print()\">Print this page</A><br><br></div>";
   printDeputyWarning();

   echo "<div class=numedeputattitlu>" . $rdep['name'] . "</div>";


   echo "<table cellspacing=10 cellpadding=10 width=800><td>";
   echo "<img src=http://www.cdep.ro" . $rdep['imgurl'] . " width=283><br>";
   echo "</td><td valign=top class=info>";
   
   // ----------------- now print the times in office, if need be
   echo "<br>În Camera Deputaților între: <b>";
   echo date("M Y", $timein) . " - " . ($timeout == 0 ? 'prezent' : date("M Y", $timeout));
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

   echo "<br>Prezență la vot de <b><span class=$class>" . (floor(10000 * $percent) / 100) . "%</span></b>, din ";
   echo $candidateVotes . " voturi posibile.<br>";

   echo "<br>Luări de cuvânt: " . getVideoCellText2($rdep['idv'], $rdep['sessions'], $rdep['seconds']);

   echo "<br><br>Partid: ";
   $parties = getPartiesFor($rdep['id']);
   echo getPartyName($parties[0]['name']);

   if (sizeof($parties) > 1) {
     echo ' (<span class="gray small">';
     for ($i = 1; $i < sizeof($parties); $i++) {
       echo "<b>" . getPartyName($parties[$i]['name']) . "</b> până în " . date("M Y", $parties[$i]['t'] / 1000);
       if ($i != sizeof($parties) - 1) {
       echo ", ";
       }
     }
     echo '</span>)';
   }
   if ($rdep['dep_url']) {
     echo "<br><br>Candidează la: " . getLinkFromThinkopolisUrl($rdep['col_url']);
   } else {  
   echo "<br>Nu pare să candideze în 2008.<br>";
   }

   echo "<br><br>Informează-te despre această persoană: <ul class=moreinfolist>";
   echo " <li> <a href=\"http://www.cdep.ro/pls/parlam/structura.mp?idm=". $rdep['idm'] . "&cam=2&leg=2004\">site-ul cdep.ro</a></li> ";
   if ($rdep['dep_url']) {
     echo " <li> <a href=\"http://www.alegeri-2008.ro". $rdep['dep_url'] . "\">alegeri-2008.ro</a></li>";
   } 
   echo " <li> <a href=\"http://www.google.ro/search?hl=ro&q=" . $rdep['name'] . "&meta=lr%3Dlang_ro\">căutare google</a></li>";
   echo " <li> <a href=\"http://www.google.ro/search?hl=ro&q=" . $rdep['name'] . "+site:wikipedia.org&&btnI=Mă Simt Norocos&meta=lr%3Dlang_ro\">wikipedia</a></li>";
   echo "</ul>";


   echo "</td><tr><td colspan=2>";
   if ($percent < 0.8) {
   //echo "<img src=deputati_ro_us_big.png>";
     echo "<img src=graph_big.png height=400>";
     ?>
     <br>Datele pentru absenteismul deputaților români este luat din agregarea voturilor publice de pe <a href="http://www.cdep.ro">cdep.ro</a>. <br>
     Datele pentru absenteismul deputaților americani <a href="http://projects.washingtonpost.com/congress/110/house/vote-missers/">este luat de aici</a>.
     <?
   }

   echo "</td></table>";
}


/**
 * From a thinkopolis URL returns a nice formatted link.
 */
function getLinkFromThinkopolisUrl($url) {
  if ($url == "") {
  return "";
  }
  $url = str_replace('Candidati-', 'Bucuresti-', $url);

  $parts = split('/', $url);
  $urlPart = str_replace('Candidati', 'Bucuresti', $parts[sizeof($parts) - 1]);
  $str = $urlPart . "<br><span class=small>";
  $str .= "<a href=$url>pe thinkopolis</a>";

  $judetParts = split('-', $urlPart); 
  $judet = $judetParts[0];
  $str .= ", <a href=\"http://www.google.ro/search?hl=ro&q=" . $judet . "+candidati+site:alegeri-2008.ro&&btnI=Mă Simt Norocos&meta=lr%3Dlang_ro\">".
          "pe alegeri-2008.ro</a></span>";
  //http://www.alegeri-2008.ro/candidati/bacau-4/
  return $str;
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
  $s = mysql_query("select distinct(idv) from votes");
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

  $sql = "select distinct(idv) from votes ".
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
function getPartiesFor($depid) {
  //SELECT distinct(iddep + idparty * 1000), iddep, idparty, max(time) FROM `belongs` WHERE iddep=248 group by iddep,idparty
  //"SELECT distinct(iddep + idparty * 1000), iddep, idparty, max(time) FROM `belongs` WHERE 1 group by iddep,idparty"
  $sql = "SELECT distinct(iddep + idparty * 1000), iddep, idparty, max(time) as t, name ". 
         "FROM `belongs` LEFT JOIN parties on parties.id = belongs.idparty ".
         "WHERE iddep=" . $depid . " group by iddep,idparty ".
         "ORDER BY t DESC ";
  $s = mysql_query($sql);
  $parties = array();

  while ($r = mysql_fetch_array($s)) {
    array_push($parties, $r);
  }
  return $parties;
}

function getPartyName($party) {
  if ($party == '-') {
  return 'Indep.';
  }
  return $party;
}


function getVideoCellText($idv, $sessions, $sec) {
  $h = floor($sec / 3600);
  $m = floor(($sec % 3600) / 60);
  $s = $sec % 60;

  return $h . "h " . $m . "m " . $s . "s<br><span class=\"small gray\">în " . 
      "<a href=\"http://www.cdep.ro/pls/steno/steno.lista?idv=" . $idv . "&leg=2004&idl=1\">" .$sessions . " puncte</a></span>";
}

function getVideoCellText2($idv, $sessions, $sec) {
  $h = floor($sec / 3600);
  $m = floor(($sec % 3600) / 60);
  $s = $sec % 60;

  return $h . "h " . $m . "m " . $s . "s în " . 
      "<a href=\"http://www.cdep.ro/pls/steno/steno.lista?idv=" . $idv . "&leg=2004&idl=1\">" .$sessions . " puncte</a>";
}

?>
