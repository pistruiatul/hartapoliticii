<?php
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT sen.id, sen.idperson, sen.name, idm, timein, timeout, motif, ".
    "possible, percent, idparty " .
  "FROM senat_2004_senators AS sen " .
    "LEFT JOIN senat_2004_votes_agg AS votes ".
      "ON votes.idperson = sen.idperson " .
    "LEFT JOIN senat_2004_belong_agg AS belong ".
      "ON belong.idperson = sen.idperson " .
  "WHERE sen.id = {$person->id} ";

$sdep = mysql_query($sql);
$rdep = mysql_fetch_array($sdep);

$numVotes = getSenatorsNumberOfVotes();

// Vote percentages
$timein = $rdep['timein'] / 1000;
$timeout = $rdep['timeout'] / 1000; // 1103259600 = 17 dec 2004

$t = new Smarty();

// Times in office, if need be
$t->assign('from', date("M Y", $timein));
$t->assign('to', $timeout == 0 ? 'Noiembrie 2008' : date("M Y", $timeout));

if ($rdep['timeout'] != 0) {
  $t->assign('reason', $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "");
}

// Print the party belonging during his senator years
$parties = getPartiesForSenator($rdep['idperson']);
$t->assign('party', getPartyName($parties[0]['name']));

$party_list = array();
if (sizeof($parties) > 1) {
 for ($i = 1; $i < sizeof($parties); $i++) {
   array_push($party_list, getPartyName($parties[$i]['name']) .
        " până în " . date("M Y", $parties[$i]['t'] / 1000));
 }
}
$t->assign('party_list', $party_list);

$candidateVotes = $rdep['possible'];
$percent = $rdep['percent'];

$class = "blacktext";
if ($percent < 0.5) { $class = "red"; }
if ($percent < 0.3) { $class = "brightred"; }

$t->assign('number_class', $class);

// ----------- Voting presence
$t->assign('presence', $percent * 100);

$t->assign('votes_from',
           $timein < 1188621956 ? "Sep 2007" : date("M Y", $timein));
$t->assign('votes_to', $timeout == 0 ? "Nov 2006" : date("M Y", $timeout));
$t->assign('possible', $candidateVotes);


$t->assign('idm', $rdep['idm']);

$t->display('mod_senat_2004.tpl');

?>
