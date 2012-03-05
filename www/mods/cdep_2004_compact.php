<?
// Prints all the stuff that a guy did that was in the 2004-2008
// house of reps.
// We know that the person we are talking about is $person.

$sql =
  "SELECT dep.id, dep.name, dep.idm, dep.timein, dep.timeout, dep.motif, ".
    "video.seconds, video.idv, video.sessions, ".
    "votes_agg.possible, votes_agg.percent, ".
    "belong_agg.idparty, ".
    "dep.imgurl " .
  "FROM cdep_2004_deputies as dep " .
    "LEFT JOIN cdep_2004_video AS video ON video.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_votes_agg AS votes_agg ".
      "ON votes_agg.idperson = dep.idperson " .
    "LEFT JOIN cdep_2004_belong_agg AS belong_agg ".
      "ON belong_agg.idperson = dep.idperson " .
  "WHERE dep.idperson = {$person->id} ";

$sdep = mysql_query($sql);
$rdep = mysql_fetch_array($sdep);

$numVotes = getNumberOfVotes();

// Vote percentages
$timein = $rdep['timein'] / 1000;
$timeout = $rdep['timeout'] / 1000; // 1103259600 = 17 dec 2004

$t = new Smarty();

// Print the times in office, if need be
$t->assign('from', date("M Y", $timein));
$t->assign('to', $timeout == 0 ? 'Noiembrie 2008' : date("M Y", $timeout));

if ($rdep['timeout'] != 0) {
 $t->assign('reason', $rdep['motif'] != "" ? " (" . $rdep['motif'] . ")" : "");
}

$parties = getPartiesFor($rdep['id'], 2004);
$t->assign('party', getPartyName($parties[0]['name']));

$party_list = array();
if (sizeof($parties) > 1) {
 for ($i = 1; $i < sizeof($parties); $i++) {
   array_push($party_list, getPartyName($parties[$i]['name']) . " până în " .
        date("M Y", $parties[$i]['t'] / 1000));
 }
}
$t->assign('party_list', $party_list);

$candidateVotes = $rdep['possible'];
$percent = $rdep['percent'];

$class = "blacktext";
if ($percent < 0.5) { $class = "red"; }
if ($percent < 0.3) { $class = "brightred";}

$t->assign('number_class', $class);

// ----------- Voting presence
$t->assign('presence', 100 * $percent);
$t->assign('votes_from',
           $timein < 1139201156 ? "Feb 2006" : date("M Y", $timein));
$t->assign('votes_to', $timeout == 0 ? "Nov 2008" : date("M Y", $timeout));
$t->assign('possible', $candidateVotes);

// ------------ Spoke in the parliament
$t->assign('speaking_time',
           getVideoCellText2($rdep['idv'], $rdep['sessions'],
                             $rdep['seconds']));


$t->assign('idm', $rdep['idm']);

$t->display('mod_cdep_2004.tpl');
?>
