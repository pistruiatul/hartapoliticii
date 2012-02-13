<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: /');
}

$query = trim($_GET['q']);
$query_orig = $query;

$title = "Rezultate căutare \"". htmlspecialchars($query) ."\"";
$nowarning = true;

include_once('header.php');
include_once('hp-includes/people_lib.php');

// eliminate the party name from the query string.
$query = eliminatePartyNames($query);
// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

echo <<<END
<table width=970 cellspacing=15>
<td>
<p class="smalltitle">$title</p>
END;

if ($query) {
  $persons = search($query);
}

if ($query != "") {
  // add it to the database
  mysql_query(
    "INSERT INTO log_searches(query, time, ip, num_results)
     VALUES('". mysql_real_escape_string($query) . "', " . time() . ",
     '" .$_SERVER['REMOTE_ADDR'] . "', " . count($persons) . ")");
  $ssid = mysql_insert_id();
}

// If I reached this point, I know for sure I either have one
// or zero matches, there are no ambiguities.
if (count($persons) == 0) {
  echo "Nu am găsit pe nimeni care să se potrivească.";
} else {
  for ($i = 0; $i < count($persons); $i++) {
    echo "<div class=searchresult>";
    echo "<img class=thumb src=" . $persons[$i]->getTinyImgUrl() .
         " align=absmiddle>";

    echo "<div class=name>";
    echo "<a href=\"?cid=9&id={$persons[$i]->id}&ssid=$ssid&ssp=$i\">
         {$persons[$i]->displayName}</a>";
    echo ", " . getPartyNameForId($persons[$i]->getFact('party'));
    echo "</div>
        <div class=snippet>";
    echo $persons[$i]->getHistorySnippet();
    echo "</div></div>";
  }
}


echo <<<END
</td>
</table>
END;
