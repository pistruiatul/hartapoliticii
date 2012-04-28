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

        if ($dkey == 'phone') {
          $dval = getCanonicalPhoneNumber($dval);
          // Incorrect phone number
          if (!$dval) continue;
        }

        if ($dkey == 'website') {
          $dval = getCanonicalWebsite($dval);
          // Incorrect phone number
          if (!$dval) continue;
        }

        info(" + {$dkey}: ${dval}");

        $sql = "INSERT INTO people_facts(idperson, attribute, value) " .
            "VALUES({$person->id}, 'contact/{$dkey}', '{$dval}')";
        mysql_query($sql);
      }
    }
  }
}

function getCanonicalPhoneNumber($phone) {
  // Strip +4 from prefix if any
  $phone = preg_replace('/^\+4/', '', $phone);

  // Split the number in main and extension
  $parts = preg_split('/int[ \.a-z]*/i', $phone);

  // Extract the digits for the main part
  $main = preg_replace('/[^\d]+/', '', $parts[0]);

  // Incorrect number (must have at least 10 digits)
  if (strlen($main) < 10) return 0;

  if (strlen($main) == 10) {
    // Romtelecom numbers for Bucharest have prefix (021)
    $main = preg_replace('/^(021)(\d{3})(\d{2})(\d{2})/',
        '($1)$2.$3.$4', $main);

    // Regular phone numbers have 4-digit prefixes
    $main = preg_replace('/^(?!021)(\d{4})(\d{3})(\d{3})/',
        '($1)$2.$3', $main);

    // Add extension if any
    if (count($parts) > 1) {
      $main .= "/" . preg_replace('/[^\d]+/', '', $parts[1]);
    }
  } else {
    $main = $phone; // It's >10d long, quit guessing the canon
  }

  return $main;
}


function getCanonicalWebsite($address) {
  // If it starts with http://, remove it.
  if (startsWith($address, 'http://')) {
    $address = substr($address, 7);
  }

  if (endsWith($address, '/')) {
    $address = substr($address, 0, strlen($address) - 1);
  }

  return $address;
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
