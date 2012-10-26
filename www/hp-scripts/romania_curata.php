<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;


function deleteAllContentFirst() {
  mysql_query("DELETE FROM people_history WHERE what='romaniacurata/2011'");
  mysql_query("DELETE FROM people_facts WHERE attribute='romaniacurata/2011'");
}


function addContentToPerson($name, $context, $content, $source) {
  $results = getPersonsByName($name, $context, infoFunction);
  $person = $results[0];

  info(">> add content to person: " . $person->id);
  info(" ");

  $sql = "INSERT INTO people_facts(idperson, attribute, value) " .
            "VALUES({$person->id}, 'romaniacurata/2011', '$content')";
  mysql_query($sql);

  $sql = "INSERT INTO people_history(idperson, what, url, time) " .
            "VALUES({$person->id}, 'romaniacurata/2011', '$source', 0)";
  mysql_query($sql);
}


function importFile($file_name) {
  $file_handle = fopen($file_name, "r");

  $source = "http://romaniacurata.ro";
  $person_name = "";
  $person_context = "";
  $person_content = "";

  while (!feof($file_handle)) {
    $line = fgets($file_handle);
    //echo $line;

    if (startsWith($line, '#')) {
      echo "comment: " . $line;
      continue;

    } else if (preg_match("/^sursa=(.*)/", $line, $matches)) {
      $source = $matches[1];
      info("SOURCE: " . $source);

    } else if (preg_match('/^(\d+). ([^,]+),(.*)/', $line, $matches)) {
      // Get what you got so far and see if there was a person to attach to.
      if ($person_name != "") {
        // All the text so far belonged to a person from before, let's see
        // if we can add it to the database.
        addContentToPerson($person_name, $person_context, $person_content,
                           $source);
      }

      // A new person's content begins here. Reset the content because it needs
      // to pile up again.
      $person_name = $matches[2];
      $person_context = $matches[3];
      $person_content = "";

      info($matches[1] . ". " . $matches[2]);

    } else {
      # This is just content, add it up.
      $person_content .= $line . "\n";
    }
  }
  addContentToPerson($person_name, $person_context, $person_content,
                           $source);
  fclose($file_handle);
}


function startsWith($haystack, $needle) {
  $length = strlen($needle);
  return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
  $length = strlen($needle);
  if ($length == 0) {
      return true;
  }

  $start  = $length * -1; //negative
  return (substr($haystack, $start) === $needle);
}

function infoFunction($person, $idString) {
  return $person->name . ' ' . $idString;
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php

deleteAllContentFirst();

importFile('romania_curata_1.txt');
importFile('romania_curata_2.txt');

include("../_bottom.php");
?>
</pre>
</body>
</html>
