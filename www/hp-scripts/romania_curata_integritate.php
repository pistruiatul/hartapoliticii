<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;


function deleteAllContentFirst() {
  mysql_query("DELETE FROM people_history WHERE what='romaniacurata/2012'");
  mysql_query("DELETE FROM people_facts WHERE attribute='romaniacurata/2012'");
}


function addContentToPerson($name, $context, $content, $source) {
  $results = getPersonsByName($name, $context, infoFunction);
  $person = $results[0];

  info(">> add content to person: " . $person->id);
  info(getHistoryString($person));
  info(" ");

  $sql = "INSERT INTO people_facts(idperson, attribute, value) " .
            "VALUES({$person->id}, 'romaniacurata/2012', '$content')";
  mysql_query($sql);

  $sql = "INSERT INTO people_history(idperson, what, url, time) " .
            "VALUES({$person->id}, 'romaniacurata/2012', '$source', 0)";
  mysql_query($sql);
}


function importFile($file_name) {
  global $startWith;

  $file_handle = fopen($file_name, "r");

  $source = "http://romaniacurata.ro";
  $person_name = "";
  $person_context = "";
  $person_content = "";
  $cat = "";

  $index = 0;
  while (!feof($file_handle)) {
    $line = fgets($file_handle);
    //echo $line;

    if ($index < $startWith) {
      $index++;
      continue;
    }

    if (startsWith($line, 'name=')) {
      // Get what you got so far and see if there was a person to attach to.
      if ($person_name != "") {
        info($person_name . ", " . $person_context);

        // All the text so far belonged to a person from before, let's see
        // if we can add it to the database.
        $content =
            "<div class=rc_2012_cat>" . $cat . "</div>\n" .
            trim($person_content) .
            "<div class=rc_2012_src>" .
            "<a href={$source} target=_blank><img src=/images/popout_icon.gif></a></div>";

        addContentToPerson($person_name, $person_context, $content, $source);
      }

      // A new person's content begins here. Reset the content because it needs
      // to pile up again.
      $person_name = trim(substr($line, 8));
      $person_context = "";
      $person_content = "";

    } else if (startsWith($line, 'context=')) {
      $person_context = trim(substr($line, 8));

    } else if (startsWith($line, 'source=')) {
      $source = trim(substr($line, 8));

    } else if (startsWith($line, 'cat=')) {
      $cat = trim(substr($line, 8));

    } else {
      # This is just content, add it up.
      $person_content .= $line . "\n";
    }
    $index++;
    $startWith = $index - 1;
  }
  addContentToPerson($person_name, $person_context, $person_content,
                           $source);
  fclose($file_handle);
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

$startWith = (int)$_GET['startWith'];

deleteAllContentFirst();

importFile('romania_curata_integritate.txt');

include("../_bottom.php");
?>
</pre>
</body>
</html>
