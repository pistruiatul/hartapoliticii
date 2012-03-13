<?php
// Prints all the stuff that a guy did that was in the 2004-2008 senate.
// We know that the person we are talking about is $person.

$sql =
  "SELECT people.id, people.display_name, cand.url as dep_url, ".
    "colleges.url as col_url " .
  "FROM people ".
    "LEFT JOIN alegeri_2008_candidates AS cand " .
      "ON cand.idperson = people.id " .
    "LEFT JOIN alegeri_2008_colleges AS colleges ".
      "ON cand.college_id = colleges.id " .
  "WHERE people.id = $person->id ";

$r = mysql_fetch_array(mysql_query($sql));
if ($r['dep_url']) {
 echo "A candidat la <b>" . getCollegeNameFromUrl($r['col_url']).
      "</b>, " . getAlegeriTvLink($r['display_name']) . ".";
} else {
 echo "<br>Nu pare să candideze în 2008.<br>";
}

if ($r['dep_url']) {
 echo "Mai multe informații și pe <a href=\"http://www.alegeri-2008.ro". 
      $r['dep_url'] . "\">alegeri-2008.ro</a>.";
}

?>
