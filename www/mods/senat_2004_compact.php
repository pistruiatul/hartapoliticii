<?
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT sen.id, sen.name, idm, timein, timeout, motif, ".
    "possible, percent, idparty " .
  "FROM senat_2004_senators AS sen " .
    "LEFT JOIN senat_2004_votes_agg AS votes ".
      "ON votes.idperson = sen.idperson " .
    "LEFT JOIN senat_2004_belong_agg AS belong ".
      "ON belong.idperson = sen.idperson " .
  "WHERE sen.id = $person->id ";

$sdep = mysql_query($sql);
$numVotes = getSenatorsNumberOfVotes();

$rdep = mysql_fetch_array($sdep);

// Vote percentages 
$timein = $rdep['timein'] / 1000;
$timeout = $rdep['timeout'] / 1000; // 1103259600 = 17 dec 2004

echo "<div>";

// Times in office, if need be
echo "Senator între <b>";
echo date("M Y", $timein) . " și " . 
     ($timeout == 0 ? 'Noiembrie 2008' : date("M Y", $timeout));
if ($rdep['timeout'] != 0) {
  echo $rdep['motif'] != "" ? "(" . $rdep['motif'] . ")" : "";    
}
echo "</b>";

// Print the party belonging during his senator years
echo ", din partea <b>";
$parties = getPartiesForSenator($rdep['id']);
echo getPartyName($parties[0]['name']);
echo "</b>.";

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

$candidateVotes = $rdep['possible'];
$percent = $rdep['percent'];

$class = "blacktext";
if ($percent < 0.5) { $class = "red"; }
if ($percent < 0.3) { $class = "brightred"; }

// ----------- Voting presence
echo "<br>";
if ($candidateVotes) {
  echo "Prezent la <b><span class=$class>" . 
       (floor(10000 * $percent) / 100) . "%</span></b> din ";
  echo " voturile electronice dintre ";

  echo $timein < 1188621956 ? "Sep 2007" : date("M Y", $timein);
  echo " și ";
  echo $timeout == 0 ? "Nov 2006" : date("M Y", $timeout);
  echo " ($candidateVotes).";
}

echo "<p>Mai multe detalii pe site-ul " .
     "<a href=\"http://www.cdep.ro/pls/parlam/structura.mp?idm=".
     $rdep['idm'] . "&cam=1&leg=2004\">site-ul cdep.ro</a></li> ";

echo "</p>";
echo "</div>";

?>