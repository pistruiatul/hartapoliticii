<?
// -------------------------------------------------------------------
// -----------------------------------------------------------------
// --------------------------- D U M P    O L D    C O D E -----------

/**
 * Should compute the aggregate votes and presence numbers, based on the 
 * raw stuff.
 * For now, this method is not used almost at all.
 * TODO(vivi) Simplify this method.
 */
function computeSenatePresenceAggregates() {
  global $cid;

  $sql = "select " .
    "id, idperson, name, name_diacritics, idm, timein, ".
    "timeout, motif ".
    "from senat_2004_senators ";

  $sdep = mysql_query($sql);

  // the absolute number of total votes
  $absoluteTotalVotes = 0;
  $votesPerParty = array();

  //mysql_query("delete from votes_senators_agg where 1"); 
  //mysql_query("delete from senators_belongs_agg where 1"); 

  $numVotes = getSenatorsNumberOfVotes();
  $count = 1;

  while ($rdep = mysql_fetch_array($sdep)) {
    // ------------------ vote percentages 
    $timein = $rdep['timein'] / 1000;
    $timeout = $rdep['timeout'] / 1000;
    // 1103259600 = 17 dec 2004
    // 1076994000

    // ----------------- now print the times in office, if need be
    $sql = 
      "select vote, count(*) as cnt " .
      "from senat_2004_votes " . 
      "where idsen = " . $rdep['id'] . " " .
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
    $sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-'];

    $candidateVotes = getSenatorNumberOfVotesBetween($timein, $timeout);
    if ($candidateVotes != 0) {
      $candidateVotes = $candidateVotes == -1 ? $numVotes : $candidateVotes; 
      $awayVotes = getAwayVotes($rdep['id'], 1);
      $candidateVotes -= $awayVotes;
      $candidateVotes = $candidateVotes < 0 ? $sum : $candidateVotes;
      $percent = 1.0 * $sum / $candidateVotes;
    }

    echo "" . (floor(10000 * $percent) / 100) . " %, ";
    if ($candidateVotes != $numVotes) {
      echo " " . $candidateVotes . " voturi, ";
      if ($awayVotes != 0) {
        echo ", pentru $awayVotes a fost europarlamentar";
      }
      echo "<br>";
    }

    $parties = getPartiesForSenator($rdep['id']);
    echo getPartyName($parties[0]['name']);
    echo "<br>";
    
    $sql = "insert into senat_2004_belongs_agg(idsen, idparty, idperson) " .
           "values(" . $rdep['id'] . ", " . 
                  $parties[0]['idparty'] . ",".
                  $rdep['idperson'] . ")";
    $sexists = mysql_query(
      "select * from senat_2004_belong_agg ".
      "where idperson={$rdep['idperson']}");
    if (mysql_num_rows($sexists) == 0 && $parties[0]['idparty'] != "") {
      echo $sql . "<br>";
      //mysql_query($sql);
    }
    // How about we make a big fat sql for the summary here.
    //$sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-']
    //$candidateVotes, $percent
    $insertAgg = 
      "insert into senat_2004_votes_agg(idsen, vda, vnu, vab, vmi, ".
          "possible, percent, idperson) " .
      "values(" . $rdep['id'] . ", " . $vt['DA'] . ", " . $vt['NU'] . ", " .
              $vt['Abţinere'] . ", " . $vt['-'] .
              ", " . $candidateVotes . ", " . $percent . 
              ", {$rdep['idperson']})";
    
    $sexists = mysql_query(
      "select * from senat_2004_votes_agg ".
      "where idperson={$rdep['idperson']}");
    if (mysql_num_rows($sexists) == 0) {
      echo '' . $insertAgg . '<br>';
      //mysql_query($insertAgg);
    }
  }
}



/**
 * Should compute the aggregate votes and presence numbers, based on the raw stuff.
 * For now, this method is not used almost at all.
 * TODO(vivi) Simplify this method.
 */
function computeDeputiesPresenceAggregates() {
  global $cid;

  $sql = 
    "select id, idperson, name, idm, timein, timeout, motif ".
    "from cdep_2004_deputies as deputies ";

  $sdep = mysql_query($sql);

  // the absolute number of total votes
  $absoluteTotalVotes = 0;
  $votesPerParty = array();
  $possibleVotesPerParty = array();

  //mysql_query("delete from cdep_2004_votes_agg where 1"); 
  //mysql_query("delete from cdep_2004_belong_agg where 1"); 

  $numVotes = getNumberOfVotes();
  $count = 1;

  while ($rdep = mysql_fetch_array($sdep)) {
    // ------------------ vote percentages 
    $timein = $rdep['timein'] / 1000;
    $timeout = $rdep['timeout'] / 1000;
    // 1103259600 = 17 dec 2004

    $count++;

    // ----------------- now print the times in office, if need be
    $sql = 
      "select vote, count(*) as cnt " .
      "from cdep_2004_votes " . 
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
    $sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-'];

    $candidateVotes = getNumberOfVotesBetween($timein, $timeout);
    $candidateVotes = $candidateVotes == -1 ? $numVotes : $candidateVotes;
 
    $awayVotes = getAwayVotes($rdep['id'], 2);
    $candidateVotes -= $awayVotes;

    $percent = 1.0 * $sum / $candidateVotes; 

    echo "" . (floor(10000 * $percent) / 100) . "% ";
    if ($candidateVotes != $numVotes) {
      echo " din " . $candidateVotes . " voturi</span>";
      if ($awayVotes != 0) 
        echo "<br>Pentru $awayVotes a fost europarlamentar";
    }

    $parties = getPartiesFor($rdep['id']);
    echo getPartyName($parties[0]['name']);
    echo "<br>";
    $sql = "insert into cdep_2004_belong_agg(iddep, idparty, idperson) ".
           "values(" . $rdep['id'] . ", " . $parties[0]['idparty'] . 
           ", " . $rdep['idperson'] . ")";
    $sexists = mysql_query(
      "select * from cdep_2004_belong_agg ".
      "where idperson={$rdep['idperson']}");
    if (mysql_num_rows($sexists) == 0) {
      echo $sql . "<br>";
      mysql_query($sql);
    }

    // How about we make a big fat sql for the summary here.
    //$sum = $vt['DA'] + $vt['NU'] + $vt['Abţinere'] + $vt['-']
    //$candidateVotes, $percent
    $insertAgg = 
      "insert into cdep_2004_votes_agg(iddep, idperson, vda, vnu, vab, vmi, ".
                            "possible, percent) " .
      "values({$rdep['id']}, {$rdep['idperson']}, {$vt['DA']}, " . 
      $vt['NU'] . ", {$vt['Abţinere']}, " . $vt['-'] .
      ", " . $candidateVotes . ", " . $percent . ")";
    
    $sexists = mysql_query(
      "select * from cdep_2004_votes_agg ".
      "where idperson={$rdep['idperson']}");
    if (mysql_num_rows($sexists) == 0) {
      echo '' . $insertAgg . '<br>';
      mysql_query($insertAgg);
    }
  }
}
?>