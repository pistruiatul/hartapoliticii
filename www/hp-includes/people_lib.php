<?php
require("person_class.php");

/**
 * @fileoverview This is a file with libraries related to people. It handles
 * identifying people by their name and potentially extra information, it
 * handles adding new people to the database correctly and consistently.
 *
 * Most of the operations regarding the people table should go through here
 * more or less.
 */
$FLAG_CAN_CHANGE_DB = false;
$MAX_QUERIES_BEFORE_COMMIT = 100;

/**
 * A global hash with all the persons in the database. We need to duplicate
 * the information here so that we can do complicated matching on it to find
 * people, operations we can't do very well in mysql.
 *
 * @type {HashMap.<String, Person>}
 */
$people = loadPeopleFromDb();
$parties = loadPartiesFromDb();

maybeAddDisambiguationResolver();
$ambiguities = loadAmbiguitiesFromDb();
maybeTestForCommitCookie();

$queries = 0;

/**
 * Returns a list of persons from the people database that could potentially
 * be the persons for this name.
 *
 * @param {string} name The name of the person we are looking for.
 * @param {string} opt_idString A string that should identify the name we are
 *     searching for in the logs, when we print it. A good example is when
 *     adding names from the 2008 elections, the id string could represent the
 *     college he is running for (which narrows down the identification).
 * @param {Function} A function to call in order to get extra information
 *     about a person found as a match and display it in the extra info field.
 *
 * @return {Array.<Person>}
 */
function getPersonsByName($name,
                          $opt_idstring = '',
                          $opt_infoFunc = emptyInfoFunction) {
  global $people;
  global $ambiguities;
  global $queries;
  global $MAX_QUERIES_BEFORE_COMMIT;

  if ($queries >= $MAX_QUERIES_BEFORE_COMMIT) {
    printJsCommitCookieScript();
  }
  $queries++;

  // If I have an ambiguity solver, skip everything else, we know exactly
  // what we want.
  if ($ambiguities[$name]) {
    return array(getNonAmbiguousPerson($name));
  }

  $parts = array();
  $cleanName = implode(' ', Person::addNameToAllNames($parts, $name));
  $matches = array();

  if (count($people)) {
    foreach ($people as $key => $person) {
      if ($key == $cleanName ||
          $person->isSubsetOf($parts) ||
          $person->isSupersetFor($parts)) {
        $matches[] = $person;
      }
    }
  }

  // If we find more than one person, fail immediately and ask the developer
  // to resolve the ambiguity.
  if (count($matches) > 1) {
    if ($ambiguities[$name]) {
      return array(getNonAmbiguousPerson($name));
    }

    info('[' . $name . '] ' . $opt_idstring . ' - se potivește cu:');
    foreach ($matches as $m) {
      printMatchDecision($name, $m);
    }
    printResolveAmbiguityToHimself($name);
    die('This ambiguity needs to be resolved before we proceed.');

  } else if (count($matches) == 1) {
    $m = $matches[0];
    // If this name is a subset or superset - if the match is not exact
    if ($m->name != $cleanName) {
      info("------ Found one non exact match --------");

      info(sprintf("[%35s] %s - se potrivește cu", $name, $opt_idstring));
      printMatchDecision($name, $matches[0]);
      printResolveAmbiguityToHimself($name);
      if (function_exists("shouldAddDetailsFunction")) {
        shouldAddDetailsFunction($name);
      }
      die('This ambiguity needs to be resolved before we proceed.');
    }
  }

  if (count($matches) == 1) {
    info(sprintf("Found  {%40s} %20s / %20s", $name, $opt_idstring,
                 $opt_infoFunc($matches[0], $opt_idstring)));
  }
  return $matches;
}


/**
 * Returns a list of persons from the people database that could potentially
 * be the persons for this name.
 *
 * @param {string} name The name of the person we are looking for.
 * @param {string} opt_idString A string that should identify the name we are
 *     searching for in the logs, when we print it. A good example is when
 *     adding names from the 2008 elections, the id string could represent the
 *     college he is running for (which narrows down the identification).
 * @param {Function} A function to call in order to get extra information
 *     about a person found as a match and display it in the extra info field.
 *
 * @return {Array.<Person>}
 */
function search($name) {
  global $people;

  $parts = array();
  $cleanName = implode(' ', Person::addNameToAllNames($parts, $name));
  $matches = array();
  $scores = array();

  if (count($people)) {
    foreach ($people as $key => $person) {
      if ($key == $cleanName ||
          $person->isSubsetOf($parts) ||
          $person->isSupersetFor($parts)) {
        $score =
          min(Person::getApproxSubsetDistance($person->allNames, $parts),
              Person::getApproxSubsetDistance($parts, $person->allNames));

        $matches[] = $person;
        $scores[] = $score;
        array_multisort($scores, SORT_ASC, SORT_NUMERIC, $matches);
      }
    }
  }
  return $matches;
}


/**
 * Returns true if the query represents this person perfectly. We do this so
 * that for the API, when we search for a complete and exact name, we're
 * pretty sure that's the person you are looking for and we don't need to
 * return weaker matches.
 *
 * @param $query
 * @param $person
 * @return {Boolean}
 */
function personQueryIsNavigational($query, $person) {
  $parts = array();
  // Makes the query into an alphabetically sorted array of cleaned up names.
  // For example, from "Pereș Alexandru" this will return 'alexandru peres'
  $cleanName = implode(' ', Person::addNameToAllNames($parts, $query));

  return $cleanName == $person->name;
}

/**
 * Returns an empty string. Used as a defualt for when passing in pointers to
 * other functions.
 * @param {Person} person The person for which we need to display some extra
 * information.
 * @return {string} The empty string.
 */
function emptyInfoFunction($person, $idString) {
  return "";
}


/**
 * Returns the person that results from solving an ambiguity based on the name
 * passed in as a parameter.
 *
 * @param {string} name The name that we believe was ambiguous and that we
 *     expect to find in the global $ambiguities variable.
 * @return {Person} A reference to the person that resulted from solving the
 *     ambiguity, either an already existing person or a new person if the
 *     resolver was not a person from the table.
 */
function getNonAmbiguousPerson($name) {
  global $people;
  global $ambiguities;
  global $FLAG_CAN_CHANGE_DB;

  if (!$ambiguities[$name]) {
    info("Invalid call to getNonAmbiguousPerson for [" . $name . "]");
    exit();
  }

  $resolveString = "Resolve '$name' =>";
  $newName = $ambiguities[$name];

  $parts = array();
  $cleanName = implode(' ', Person::addNameToAllNames($parts, $newName));

  if ($people[$cleanName]) {
    info("$resolveString existing [" . $people[$cleanName]->toString() . "]");
    return $people[$cleanName];

  } else {
    $p = new Person();
    $p->setName($newName);
    $p->setDisplayName($name);

    if ($FLAG_CAN_CHANGE_DB) {
      info("$resolveString new person [" . $p->toString() . "]");
      $p->addToDatabaseIfNobody();

    } else {
      info("Should add {{$p->displayName}}");
      if (function_exists("shouldAddDetailsFunction")) {
        info(shouldAddDetailsFunction($name));
      }
    }

    return $p;
  }
}


/**
 * Prints the matching decision between a name and a person. The method
 * attempts to print extra information about the person that we thing this
 * matches so we can make a good decision about whether this is the right
 * match.
 */
function printMatchDecision($name, $person) {
  info(getResolveString($name, $person->name) . " " . $person->toString());
  info("        " . getHistoryString($person));
}


/**
 * Returns a string that leads to resolving a name to a different name.
 */
function getResolveString($oldName,
                          $newName,
                          $opt_anchorText = "pick") {
  return "<a href=?ambig=" . urlencode("$oldName,$newName") .
         ">$opt_anchorText</a>";
}


/**
 * Returns a compact string with the entire history of a particular person.
 */
function getHistoryString($person) {
  $arr = array();
  // Fetch info from the history file.
  $s = mysql_query("SELECT * FROM people_history WHERE idperson=$person->id");
  while ($r = mysql_fetch_array($s)) {
    $url = $r['url'];
    $what = $r['what'];
    $detailsString = getHistoryDetailsString($person, $what);

    $arr[] = "<a href=$url>$what</a> $detailsString";
  }
  return "<a href=/?cid=9&id={$person->id}>page</a> / ".
         implode(", ", $arr);
}


/**
 * Returns a string with details for a particular person and a specified part
 * of their history. For example, if we know that person X was in
 * 'alegeri/2008' we are interested in some details about that, like the
 * college that he was in, that will better help us identify the person.
 *
 * For now this is more or less manual, for each history item we will be
 * interested in different details, fetched in different ways.
 *
 * @param {Person} person The person we are interested in.
 * @param {string} what The type of history item we are looking for.
 * @return {string} A string with the details.
 */
function getHistoryDetailsString($person, $what) {
  switch ($what) {
    case 'alegeri/2008':
      // For the participants in the elections, the pre-elections stats and
      // information, we are interested mainly in the college they ran for.
      $s = mysql_query(
        "SELECT colleges.url FROM alegeri_2008_candidates AS candidates ".
        "LEFT JOIN alegeri_2008_colleges AS colleges ".
          "ON colleges.id = candidates.college_id ".
        "WHERE candidates.idperson = " . $person->id);

      if ($r = mysql_fetch_array($s)) {
        $url = $r['url'];
        $parts = split("/", $url);
        return "<a href=\"$url\">". $parts[count($parts) - 1] . "</a>";
      }
      break;

    case 'results/2008':
        // For the participants in the elections, the pre-elections stats and
        // information, we are interested mainly in the college they ran for.
        $s = mysql_query(
          "SELECT college FROM results_2008_candidates AS candidates ".
          "WHERE candidates.idperson = " . $person->id);

        if ($r = mysql_fetch_array($s)) {
          $parts = split(' ', $r['college']);
          return '<a href="http://www.becparlamentare2008.ro/'.
                    'rezul/colegii_rezultate_ora10.htm">' .
                 $parts[1] . '-' . $parts[0] . '</a>';
        }
        break;
  }
}


/**
 * Prints a string asking the developer to resolve this person to himself, in
 * case he doesn't match any of the suggested matches and we consider him a
 * new person.
 * @param {string} name The name of the person to resolve to himself.
 */
function printResolveAmbiguityToHimself($name) {
  info('');
  info("or <a href=?ambig=" .
       urlencode("$name," . strtolower($name)) .
       ">himself</a>, as a new person.");
  info('');
}


/**
 * Adds an item of history to the history of a person. This is used to provide
 * context for a certain person ID, to add a piece of fact that we know, with
 * a what and an url so we can look up what we are refering to.
 *
 * @param {int} id The id of the person we want to add history for.
 * @param {string} what The type of history item (i.e. 'senat/2004',
 *     'cdep/2004', 'catavencu/list/nov_2004', etc.)
 * @param {string} url The url where we can find this person as part of this
 *     fact. This is used for example to link to the page of the person on the
 *     cdep site so we can look him up and make sure it's the same person.
 * @param {long} time The time at which this piece of history happened.
 */
function addPersonHistory($idperson, $what, $url, $time) {
  global $FLAG_CAN_CHANGE_DB;

  if ($what && $idperson > 0) {
    $s = mysql_query(
      "SELECT * FROM people_history " .
      "WHERE idperson=$idperson AND what='$what'");
    if (mysql_num_rows($s) == 0 && $FLAG_CAN_CHANGE_DB) {
      mysql_query(
        "INSERT INTO people_history(idperson, what, url, time) ".
        "VALUES($idperson, '$what', '$url', $time)");
    }
  }
}


/**
 * Adds a person to the people database, based on a name and a display name.
 *
 * @param {string} name The name of this person.
 * @param {string} displayName The name that this person will be displayed
 *     under.
 * @return {Person} A reference to the new Person object.
 */
function addPersonToDatabase($name, $displayName) {
  global $FLAG_CAN_CHANGE_DB;
  global $people;

  $person = new Person();
  $person->setName($name);
  $person->addExtraNames($displayName);
  $person->setDisplayName($displayName);

  if ($FLAG_CAN_CHANGE_DB) {
    info("Adding person [" . $person->toString() . "]");
    $person->addToDatabaseIfNobody();
    $people[$person->name] = $person;
  } else {
    info("Should add {{$person->displayName}}");
    if (function_exists("shouldAddDetailsFunction")) {
      info(shouldAddDetailsFunction($name));
    }
  }
  return $person;
}


/**
 * Loads the people that are currently in the people table in our database.
 *
 * @return {HashMap.<String, Person>} The hash table with the people, where
 *     the key is the name of each person (lower case, no diacritics, all
 *     names sorted alphabetically).
 */
function loadPeopleFromDb() {
  $results = array();
  $s = mysql_query("SELECT * FROM people");
  while($r = mysql_fetch_array($s)) {
    $person = new Person();
    $person->setName($r['name']);
    $person->setDisplayName($r['display_name']);
    $person->setId($r['id']);

    $results[$person->name] = $person;
  }
  return $results;
}


/**
 * Loads the parties that are currently in the parties table in our database.
 *
 * @return {HashMap.<String, Object>} The hash table with the people, where
 *     the key is the name of each person (lower case, no diacritics, all
 *     names sorted alphabetically).
 */
function loadPartiesFromDb() {
  $results = array();
  $s = mysql_query("SELECT * FROM parties");

  while($r = mysql_fetch_array($s)) {
    $party = array();
    $party['name'] = $r['name'];
    $party['name'] = $r['name'];
    $party['id'] = $r['id'];

    $results[$r['id']] = $party;
  }
  return $results;
}


/**
 * Loads the disambiguation pairs from the database, for the current URL key.
 * For explanations about how this works, please check the design doc.
 * TODO(vivi) Write more detailed and accurate information in the design doc.
 *
 * @return {HashMap.<String, String>} Pairs of names, where the key is the
 *     name that will need to be disambiguated when met and the value is the
 *     name that it will resolve to.
 */
function loadAmbiguitiesFromDb() {
  $reqUri = getUriKey();
  $s = mysql_query("SELECT * FROM people_ambiguities WHERE url='$reqUri'");
  $results = array();
  while ($r = mysql_fetch_array($s)) {
    $results[$r['name']] = $r['resolve_to'];
  }
  return $results;
}


/**
 * Checks whethers the url has an ambiguity resolver to add to the database
 * for the current script URL. If so, the method adds the disambiguation.
 */
function maybeAddDisambiguationResolver() {
  // If there's an ambiguity string to resolve and add to the data base,
  // do that.
  if ($_GET['ambig']) {
    $reqUri = getUriKey();

    // Decode te disambiguation string from the url
    list($from, $to) = split(',', urldecode($_GET['ambig']));
    $from = mysql_real_escape_string(trim($from));
    $to = mysql_real_escape_string(trim($to));

    $s = mysql_query(
      "SELECT * FROM people_ambiguities " .
      "WHERE name='$from' AND resolve_to='$to' AND url='$reqUri'");
    if (mysql_num_rows($s) == 0) {
      mysql_query(
        "INSERT INTO people_ambiguities(url, name, resolve_to) " .
        "VALUES('$reqUri', '$from', '$to')");
    }
  }
}


/**
 * Returns a key representing the current url from where we run this method.
 * This is mainly used to identify individual disambiguations for each script
 * that we are running to add people to our database.
 *
 * @return {String} The path part of the script.
 */
function getUriKey() {
  list($reqUri, $junk) = split('\?', $_SERVER["REQUEST_URI"]);
  return $reqUri;
}


/**
 * Writes the javascript method that will set the cookie that indicates that
 * we need to commit and then redirects the page to trigger that commit.
 */
function printJsCommitCookieScript() {
  ?>
  <script>
    function setCommitCookieAndRedirect() {
      var now = new Date();
      // expires in 10 seconds
      var expires = new Date(now.getTime() + 1000 * 10);

      document.cookie = "hp_commit=yes" +
          ";expires=" + expires.toGMTString() + ";path=/";

      var parts = document.location.href.split("?");
      var baseUrl = parts[0];
      document.location.href = baseUrl + "?rand=" + Math.random() * 10000;
    }
  </script>
  <?php
  die("Time to ".
      "<a href=\"javascript:setCommitCookieAndRedirect();\">commit</a>.");
}


/**
 * Tests for the existance of a commit cookie. If the cookie exists it sets
 * the flag to change the database and then deletes the cookie.
 */
function maybeTestForCommitCookie() {
  global $FLAG_CAN_CHANGE_DB;

  if ($_COOKIE['hp_commit']) {
    setCookie('hp_commit', 'erased', 1);
    echo '<pre>';
    info('[ --- Commit cookie found! This is a live round. ----]');
    echo '</pre>';
    $FLAG_CAN_CHANGE_DB = true;
  }
}



/**
 * Downloads the photo passed in as an URL and saves it locally for the person
 * id specified as a parameter
 *
 * @param {Number} $person_id The id of the person for which we are downloading
 *     this photo.
 * @param {String} $url The url where the photo should be found.
 * @return void
 */
function downloadPersonPhoto($person_id, $url) {
  $parts = explode(".", $url);
  $ext = strtolower(array_pop($parts));

  // This is pretty dumb here. :-)
  if (strlen($ext) > 4) {
    $ext = 'jpg';
  }

  // Try to figure out the first file name that's not taken for this person.
  // Multiple photos could be uploaded for each politician.
  $fname = "../images/people/{$person_id}.{$ext}";
  $count = 1;
  while (is_file($fname)) {
    $fname = "../images/people/{$person_id}_{$count}.{$ext}";
    $count++;
  }

  $fp = fopen($fname, "w");

  // Now actually get the photo
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);

  curl_exec($ch);
  curl_close($ch);

  fclose($fp);
}

?>
