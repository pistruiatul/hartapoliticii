<?
/**
 * Private helper function for a stats object where I add up stuff.
 * This is a very temporary function, I should delete it I think.
 */
function initStatsObject() {
  $stats = array();
  $stats['winners'] = 0;
  $stats['losers'] = 0;

  $stats['winnersPresence'] = 0;
  $stats['losersPresence'] = 0;

  $stats['swinners'] = 0;
  $stats['dwinners'] = 0;
  return $stats;
}


/**
 * Given a stats object, a senator object from the sql query and the percent
 * of votes that he had, properly count him under various statistics.
 */
function countStats($stats, $rdep, $percent) {
  // Count some numbers;
  if ($rdep['col_url']) {
    if ($rdep['reason'] == '') {
      $stats['scandidates']++;
      if ($percent < 0.5) {
        $stats['underFiftyS']++;
      }
    } else {
      if ($rdep['winner'] == 1) {
        $stats['winners']++;
        $stats['winnersPresence'] += $percent;
        if (substr($rdep['college'], 0, 1) == 'S') {
          $stats['swinners']++;
        } else {
          $stats['dwinners']++;
        }
      } else if ($rdep['winner'] == 0) {
        $stats['losers']++;
        $stats['losersPresence'] += $percent;
      }
    }
  }
  return $stats;
}


function printStats($stats) {
  echo $stats['winners'] . " " . $stats['underFiftyWinners'] . " " . 
       ($stats['winnersPresence'] / $stats['winners']);
  echo "<br> " . $stats['losers'] . " " . $stats['underFiftyLosers'] . " " . 
       ($stats['losersPresence'] / $stats['losers']);
  echo "<br>s:" . $stats['swinners'] . " d:" . $stats['dwinners'];
}


?>