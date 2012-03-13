<?php

// Prints all the stuff that a guy did that was in the 2004-2008
// house of reps.
// We know that the person we are talking about is $person.
function mod_senat_2008_get_parties($id) {
  $sql =
  "SELECT distinct(iddep + idparty * 1000), iddep, idparty, ".
    "max(time) as t, name ".
  "FROM `senat_2008_belong` ".
    "LEFT JOIN parties ON parties.id = senat_2008_belong.idparty ".
  "WHERE iddep=" . $id . " group by iddep,idparty ".
  "ORDER BY t DESC ";

  $s = mysql_query($sql);
  $parties = array();

  while ($r = mysql_fetch_array($s)) {
    array_push($parties, $r);
  }
  return $parties;
}


function mod_senat_2008_summary() {
  global $parties;
  global $person;
  global $cid;

  $t = new Smarty();

  $sql =
    "SELECT dep.id, dep.name, dep.idm, dep.timein, dep.timeout, dep.motif, ".
      //"video.seconds, video.idv, video.sessions, ".
      "votes_agg.possible, votes_agg.percent, votes_agg.maverick, ".
      "belong_agg.idparty ".
    "FROM senat_2008_senators as dep " .
      // "LEFT JOIN cdep_2004_video AS video ON video.idperson = dep.idperson " .
      "LEFT JOIN senat_2008_votes_agg AS votes_agg ".
        "ON votes_agg.idperson = dep.idperson " .
      "LEFT JOIN senat_2008_belong_agg AS belong_agg ".
        "ON belong_agg.idperson = dep.idperson " .
    "WHERE dep.idperson = {$person->id} ";

  $sdep = mysql_query($sql);
  $rdep = mysql_fetch_array($sdep);

  $numVotes = getNumberOfVotes();

  // Vote percentages
  $timein = $rdep['timein'] / 1000;
  $timeout = $rdep['timeout'] / 1000;

  // Print the times in office, if need be
  $t->assign('dep_time_in', date("M Y", $timein));
  $t->assign('dep_time_out', $timeout == 0 ? 'prezent' : date("M Y", $timeout));

  if ($rdep['timeout'] != 0) {
    $t->assign('dep_motif', $rdep['motif'] != "" ?
                            " (" . $rdep['motif'] . ")" : "");
  }

  $parties = mod_senat_2008_get_parties($rdep['id']);
  $t->assign('dep_party', getPartyName($parties[0]['name']));

  // -----------------------------------------------------
  // TODO(vivi): Also move this into a nice template loop!
  // -----------------------------------------------------
  if (sizeof($parties) > 1) {
   $extraParties = ' (<span class="gray small">';
   for ($i = 1; $i < sizeof($parties); $i++) {
     $extraParties .= "<b>" . getPartyName($parties[$i]['name']) . "</b> până în " .
          date("M Y", $parties[$i]['t'] / 1000);
     if ($i != sizeof($parties) - 1) {
       $extraParties .= ", ";
     }
   }
   $extraParties .= '</span>)';
  }

  $candidateVotes = $rdep['possible'];
  $percent = $rdep['percent'];

  $class = "blacktext";
  if ($percent < 0.5) { $class = "red"; }
  if ($percent < 0.3) { $class = "brightred";}

  $maverick = $rdep['maverick'];

  $t->assign('dep_percent', 100 * $percent);
  $t->assign('maverick', 100 * $maverick);
  $t->assign('dep_possible_votes', $candidateVotes);

  $t->assign('dep_idm', $rdep['idm']);
  $t->assign('cid', $cid);
  $t->assign('idperson', $person->id);

  $t->assign('chd1', 100 * ($percent - $percent * $maverick));
  $t->assign('chd2', 100 * ($percent * $maverick));
  $t->assign('chd3', 100 * (1 - $percent));

  $t->display('mod_senat_2008_summary.tpl');
};


/**
 * A method that selects the most recent votes that this person has voted in,
 * and then displays them.
 */
function mod_senat_2008_get_votes($person, $count, $start=0, $maverick=-1) {
  $maverickText = $maverick >= 0 ? "AND maverick = {$maverick}" : "";
  $sql = "
    SELECT v.vote, v.link, v.maverick,
           d.type, d.description, d.time, d.vda, d.vnu, d.vab, d.vmi,
           l.link as law_link, l.number as law_number
    FROM senat_2008_votes AS v
    LEFT JOIN senat_2008_votes_details AS d ON d.link = v.link
    LEFT JOIN senat_2008_laws AS l ON l.id = v.idlaw
    WHERE v.idperson = {$person->id}
        AND d.type NOT LIKE 'Prezen%'
        AND d.type NOT LIKE 'Stabilire timpi dezbatere%'
        AND d.type NOT LIKE 'misc'
        {$maverickText}
    ORDER BY time DESC
    LIMIT {$start}, {$count}";

  $s = mysql_query($sql);
  $votes = array();
  while ($r = mysql_fetch_array($s)) {
    $vote = $r;
    $vote['time'] = $vote['time'] / 1000;

    $votes[] = $vote;
  }
  return $votes;
}


?>
