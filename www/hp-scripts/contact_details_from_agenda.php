<?php
require("../_top.php");

function importContactDetails()
{
  $agenda_url = "http://agenda.grep.ro/download?format=json";
  $agenda = file_get_contents($agenda_url, 0, null, null);
  $agenda_output = json_decode($agenda);

  $detail_keys = array('website',
                       'email',
                       'phone',
                       'address',
                       'facebook',
                       'twitter'
                       );

  foreach ($agenda_output->persons as $person) {
    foreach ($detail_keys as $dkey) {
      if (array_key_exists($dkey, $person)) {
        foreach ($person->$dkey as $dval) {
          echo "Importing {$dkey} of {$person->name}: ${dval}";
          $sql = "INSERT INTO people_facts(idperson, attribute, value) " .
            "VALUES({$person->id}, 'contact/{$dkey}', '{$dval}')";
          mysql_query($sql);
          echo "<br />";
        }
      }
    }
  }
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
