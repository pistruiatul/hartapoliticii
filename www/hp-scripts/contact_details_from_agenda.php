<?php
require("../_top.php");
require("../hp-includes/people_lib.php");

$MAX_QUERIES_BEFORE_COMMIT = 10000;

function importContactDetails() {
  // First, delete everything that was there already.
  mysql_query("DELETE FROM  `people_facts`
               WHERE  `attribute` LIKE  'contact/%'");

  $agenda_url = "http://agenda.grep.ro/download?format=json";
  $agenda = file_get_contents($agenda_url, 0, null, null);
  $agenda_output = json_decode($agenda);

  $detail_keys = array(
    'website',
    'email',
    'phone',
    'address',
    'facebook',
    'twitter'
  );

  foreach ($agenda_output->persons as $personData) {
    // Find this person in our database. Seems like the IDs did not translate
    // well so we have to look at the names.

    $results = getPersonsByName($personData->name, '', infoFunction);
    $person = $results[0];

    foreach ($detail_keys as $dkey) {
      if (!array_key_exists($dkey, $personData)) continue;

      foreach ($personData->$dkey as $dval) {
        info(" + {$dkey}: ${dval}");

        $sql = "INSERT INTO people_facts(idperson, attribute, value) " .
          "VALUES({$person->id}, 'contact/{$dkey}', '{$dval}')";
        mysql_query($sql);
      }
    }
  }
}

function infoFunction($person, $idString) {
  // do nothing.
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body onload="window.scrollTo(0, 1000000);">
<pre>
<?php
importContactDetails();

include("../_bottom.php");
?>
</pre>
</body>
</html>
