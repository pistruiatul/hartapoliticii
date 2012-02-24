<?
// Prints all the stuff that a guy did that was in the 2004-2008
// house of reps.
// We know that the person we are talking about is $person.

function cdep_2004_mod() {
  global $parties;
  global $person;

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

  // Print the times in office, if need be
  echo "Deputat între <b>";
  echo date("M Y", $timein) . " și " .
      ($timeout == 0 ? 'Noiembrie 2008' : date("M Y", $timeout));
  if ($rdep['timeout'] != 0) {
   echo $rdep['motif'] != "" ? " (" . $rdep['motif'] . ")" : "";
  }
  echo "</b>,";

  echo " din partea <b>";
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
  echo "</b>.";


  $candidateVotes = $rdep['possible'];
  $percent = $rdep['percent'];

  $class = "blacktext";
  if ($percent < 0.5) { $class = "red"; }
  if ($percent < 0.3) { $class = "brightred";}

  // ----------- Voting presence
  echo "<br>";
  echo "Prezent la <b><span class=$class>" .
       (floor(10000 * $percent) / 100) . "%</span></b> din ";
  echo " voturile electronice dintre ";

  echo $timein < 1139201156 ? "Feb 2006" : date("M Y", $timein);
  echo " și ";
  echo $timeout == 0 ? "Nov 2008" : date("M Y", $timeout);
  echo " ($candidateVotes).";

  // ------------ Spoke in the parliament
  echo "<br>Luări de cuvânt: " .
      getVideoCellText2($rdep['idv'], $rdep['sessions'], $rdep['seconds']);

  echo "<br><br>Mai multe detalii pe ";
  echo "<a href=\"http://www.cdep.ro/pls/parlam/structura.mp?".
      "idm=". $rdep['idm'] . "&cam=2&leg=2004\">site-ul cdep.ro</a>.";
}

cdep_2004_mod();
?>