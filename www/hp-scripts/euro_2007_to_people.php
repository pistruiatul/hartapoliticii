<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

// Functions go here.
function candidatesArePeopleToo() {
  global $FLAG_CAN_CHANGE_DB;
 
  //mysql_query("TRUNCATE TABLE euro_parliament_2007_agg");

  $s = mysql_query(
    "SELECT p.idperson, p.name, count(*) as count, t.tin, t.tout 
    FROM euro_parliament_2007 AS p 
    LEFT JOIN euro_parliament_2007_times AS t 
      ON t.name = p.name 
    WHERE idperson != 0 
    GROUP BY name 
    ORDER BY t.tout ASC, t.tin DESC");
  $i = 1;
  $p = 0;
  while($r = mysql_fetch_array($s)) {
    $name = trim($r['name']);

    $tin = $r['tin'] ? $r['tin'] : 0;
    $tout = $r['tout'] ? $r['tout'] : 0;

    $a = $tin == 0 ? 1197262800 : $tin;
    $b = $tout == 0 ? 1241668800 : $tout;

    $votes = countVotesBetween($a, $b);
    
    $p += $r['count'] / $votes;
    
    echo "$name,{$r['count']},$votes,". 
         date("M Y", $a) . ",".date("M Y", $b);
    
    if ($r['idperson'] != -1 && false) {
      mysql_query(
        "INSERT INTO 
        euro_parliament_2007_agg(idperson, present, total, tin, tout) 
        values({$r['idperson']}, {$r['count']}, $votes, $tin, $tout)
        ");
        
      mysql_query(
        "INSERT INTO people_history(idperson, what, url, time) 
        values({$r['idperson']}, 'euro_parliament/2007',  'http://www.europarl.europa.eu/members/public/geoSearch/search.do?country=RO&language=RO', 1241668800)");
        
    }

    echo "\n";
  }
  echo 100 * $p / 42;
}


function countVotesBetween($a, $b) {
  $s = mysql_query(
    "SELECT name, count(*) as cnt 
    from euro_parliament_2007 
    where time <= $b AND $a <= time 
    group by time");
    
    
  return mysql_num_rows($s);
}


?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php
candidatesArePeopleToo();

include("../_bottom.php");
?>
</body>
</html>
