<?php
function getShortPartyName($name) {
    switch($name) {
      case 'partidul democrat liberal': 
        return 'PDL';
      case 'alianta politica partidul social democrat + partidul conservator': 
        return 'PSD+PC'; 
      case 'partidul national liberal': 
        return 'PNL';
      case 'uniunea democrata maghiara din romania':
        return 'UDMR';
  }
}


function showVotesThatCount($order, $room) {
  global $cid;
  if ($order != 'difference' && $order != 'winnername' && 
      $order != 'college') {
    $order = 'difference';
  }
  if ($order == 'college') {
    $order = 'county, college_nr ';
  }
  $room_letter = $room == 'senat' ? 'S' : 'D';

  $sql = 
  "SELECT res.college, ".
    "winners.nume as winnername, res.idperson_winner, ".
    "runners.nume as runner, res.idperson_runnerup, " .
    "total, " .
    "res.difference, " .
    "winvotes, runvotes, res.reason, winparty.name as partyWinner, " .
    "runparty.name as partyRunner, county " .
  "FROM `results_2008_agg` as res ".
    "LEFT JOIN results_2008_candidates AS winners ".
      "ON winners.idperson = res.idperson_winner ".
    "LEFT JOIN results_2008_candidates AS runners ".
      "ON runners.idperson = res.idperson_runnerup ".
    "LEFT JOIN parties AS winparty ".
      "ON winparty.id = winners.idpartid ".
    "LEFT JOIN parties AS runparty ".
      "ON runparty.id = runners.idpartid ".
  "WHERE res.college LIKE '$room_letter%' " .
  "ORDER BY $order ASC ";

  $s = mysql_query($sql);
  $i = 1;
  echo "<table width=900 cellpadding=0 cellspacing=3 class=bigtable>" . 
       "<tr class=header><td></td>" . 
       "<td><a href=?cid=$cid&room=$room&order=college>".
       "Colegiu</a></td><td>".
       "<a href=?cid=$cid&room=$room&order=winnername>".
       "Câștigător</a></td>" .
       "<td>Următorul care ar fi putut câștiga</td>" .
       "<td><a href=?cid=$cid&room=$room&order=difference>".
       "Dacă ar fi avut în plus</a></td></tr>";
  $currentCounty = 'Alba';

  while ($r = mysql_fetch_array($s)) {
    $percent = floor(10000 * $r['winvotes'] / $r['total']) / 100;
    $percentRunner = floor(10000 * $r['runvotes'] / $r['total']) / 100;
    
    if ($r['county'] != $currentCounty && $order == 'county, college_nr ') {
      $currentCounty = $r['county'];
      echo '<tr class=separator><td colspan=5 height=1></td></tr>';
    }

    echo "<tr>";
    echo "<td valign=top>" . $i++ . ".</td>";
    echo "<td><a href=http://www.becparlamentare2008.ro/rezul/".
         "rep_mand_et2.htm>{$r['college']}</a>" .
         "<br><span class=\"small gray\">{$r['total']} ".
         "voturi total</span></td>";

    echo "<td><a href=?cid=9&id={$r['idperson_winner']}>" . 
         ucwords(strtolower($r['winnername'])) . 
         "</a><span class=\"small gray\"> - " .
         $r['partyWinner'] . "</span>" .
         "<br><span class=\"small gray\">{$percent}%</span></td>";

    echo "<td><a href=?cid=9&id={$r['idperson_runnerup']}>" .
         ucwords(strtolower($r['runner'])) . 
         "</a><span class=\"small gray\"> - " .
         $r['partyRunner'] . "</span>" .
         "<br><span class=\"small gray\">{$percentRunner}%</td>";
    
    echo "<td>" . $r['difference'] . " voturi " .
         "<br><span class=\"small gray\">{$r['reason']}</span></td>";

    echo "</tr>";
  }
  echo "</table>";
}

?>