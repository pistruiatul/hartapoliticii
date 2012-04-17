<?php
require('string_utils.php');

/**
 * @fileoverview This Class is identifying a person in our table. It's
 * used to send data about a person around when needed, to load a person
 * from the data base or to save one there.
 */
class Person {
  /** If this person is nobody we know. */
  const NOBODY = -1;

  /** If this person is ambigously identified by name. */
  const AMBIGOUS = -2;

  /** A constant for identifying new persons. */
  const NEW_PERSON = -3;

  const TRIM_CHARS = "\n\t\r\0 \xC2\xA0\x0B";

  /** The id of the person in the people table. */
  public $id = Person::NOBODY;

  /**
   * The exhaustive name of the person (including first name, last name,
   * middle name).
   */
  public $name;

  /** The name that this person will be displayed under. */
  public $displayName = '';

  /**
   * A sorted array with all the individual names of this person, both basic
   * and extended. All the names in this array are lower case, no diacritics.
   * This is used only internally for name matching.
   */
  public $allNames = array();


  /**
   * Sets the name of this person to the string given as a parameter.
   */
  public function setName($name) {
    $this->allNames = array();
    $this->allNames = $this->addNameToAllNames($this->allNames, $name);
    $this->name = implode(' ', $this->allNames);
  }


  /**
   * Sets the id of this person.
   */
  public function setId($id) {
    $this->id = $id;
  }


  /**
   * Returns the name of the person to be used in an URL.
   * @return {string}
   */
  public function getUrlName() {
    return str_replace(' ', '+', $this->name);
  }


  /**
   * Adds extra names to this person, if we are sure they have them. Any
   * number of extra names can be added, the names already found will be
   * safely ignored.
   *
   * As an example:
   *   $person->setName('Popescu Ion');
   *   $person->addExtraNames('Ion Popescu-Duvaz');
   *   echo 'Current name: ' . $person->name;
   *
   * Will print:
   *   Current name: duvaz ion popescu
   */
  public function addExtraNames($extraNames) {
    $this->addNameToAllNames($this->allNames, $extraNames);
    $this->name = implode(' ', $this->allNames);
  }


  public function setDisplayName($displayName) {
    $this->displayName = trim($displayName, Person::TRIM_CHARS);
    $this->displayName = ucwords(strtolower_ro($this->displayName));
  }


  public function addToDatabaseIfNobody() {
    if ($this->isNobody()) {
      $this->addToDb();
    }
  }


  public function isNobody() {
    return $this->id == Person::NOBODY;
  }


  private function isAmbiguous() {
    return $this->id == Person::AMBIGUOUS;
  }


  /**
   * Adds this person to the people database and sets the id appropriately.
   */
  private function addToDb() {
    global $people;

    mysql_query(
      "INSERT INTO people(name, display_name) ".
      "values('" . $this->name . "', '" . $this->displayName ."')");
    $this->id = mysql_insert_id();

    $people[$this->name] = $this;
  }


  /**
   * Adds this person to the people database and sets the id appropriately.
   */
  public function loadFromDb() {
    global $people;
    $s = mysql_query("SELECT * FROM people WHERE id={$this->id}");
    $r = mysql_fetch_array($s);

    $this->name = $r['name'];
    $this->displayName = $r['display_name'];
    $this->addNameToAllNames($this->allNames, $this->name);
  }

  /**
   * Adds this person to the people database and sets the id appropriately.
   */
  public function getFact($fact) {
    $s = mysql_query("SELECT * FROM people_facts WHERE idperson={$this->id}");
    while ($r = mysql_fetch_array($s)) {
      if ($r['attribute'] == $fact) {
        return $r['value'];
      }
    }
    return false;
  }

  /**
   * Updates the all names array from the name and nameExt strings. It splits
   * the names by spaces and dashes, it also strips off the diacritics into
   * english alphabet letters.
   *
   * @param {Array} allNames A reference to the array containing the existing
   *     names. This array will change as a result of calling this method.
   * @param {string} newName The string with all the new names (or old names)
   *     that will be added to the current person.
   * @return void
   */
  public function addNameToAllNames(&$allNames, $newName) {
    $partsName =
        Person::getIndividualParts(trim($newName, Person::TRIM_CHARS));

    foreach($partsName as $elem) {
      if (trim($elem, Person::TRIM_CHARS) != '') {
        $clean = getStringWithoutDiacritics(trim($elem, Person::TRIM_CHARS));
        $clean = strtolower($clean);
        if (!in_array($clean, $allNames)) {
          $allNames[] = $clean;
        }
      }
    }
    sort($allNames, SORT_STRING);
    return $allNames;
  }


  /**
   * Returns true if the current person's name is a subset of the name passed
   * in as a parameter.
   * Example:
   *   $person->setName('Ion Popescu Duvaz');
   *   assertTrue($person->isSubset('Duvaz Popescu Ion Dolanescu'));
   */
  public function isSubsetOf($name) {
    if (!is_array($name)) {
      $parts = array();
      Person::addNameToAllNames($parts, $name);
    } else {
      $parts = $name;
    }
    if (count($parts) - count($this->allNames) < 0) {
      return false;
    }

    return Person::getApproxSubsetDistance($this->allNames, $parts) <= 1;
  }


  /**
   * Returns true if the current person's name is a subset of the name passed
   * in as a parameter.
   * Example:
   *   $person->setName('Ion Popescu Duvaz');
   *   assertTrue($person->isSupersetFor('Popescu Ion'));
   */
  public function isSupersetFor($name) {
    if (!is_array($name)) {
      $parts = array();
      Person::addNameToAllNames($parts, $name);
    } else {
      $parts = $name;
    }
    if (count($parts) - count($this->allNames) > 0) {
      return false;
    }

    return Person::getApproxSubsetDistance($parts, $this->allNames) <= 1;
  }


  /**
   * Returns the approximate distance between a subset of strings and a
   * superset, but only from the subset to the superset. The purpose is to
   * determine if one array is indeed a subset of another array, within a
   * given margin of error with it's element.
   *
   * It's a little difficult to explain, so I'll give some examples:
   *
   * ['ion', 'pop'], ['pop', 'ion'] -> 0
   * ['ion', 'pop'], ['pop', 'ioan'] -> 1 // ion vs. ioan are at dist 1
   * ['ion', 'pop'], ['pop', 'ioan', 'duvaz'] -> 1 // ignore 'duvaz'
   * ['ion', 'pop', 'duvaz'], ['pop', 'ioan'] -> 4
   *     // because it has more elements than the thought superset, it will
   *     // return a large value and that's all we care about.
   *
   * ATTENTION: this method will fail for:
   *
   * ['pop', 'ion', 'ioan'], ['pop', 'ion'] -> 1
   * This is an implementation detail that would be expensive to fix for now,
   * so we just assume that this case is not really likely to happen.
   * If this does happen, the error is just a false positive which will not
   * affect the system, it will just annoy some developer with false
   * ambiguities.
   */
  public function getApproxSubsetDistance($subset, $superset) {
    $dist = 0;
    foreach ($subset as $elem) {
      $min = 100;
      foreach($superset as $haystack) {
        $min = min(distanceBetweenStrings($elem, $haystack), $min);
      }
      $dist += $min;
      // early exit so we don't have to do much computation for obvious
      // cases.
      if ($dist > 1) {
        return $dist;
      }
    }
    return $dist;
  }

  private function getIndividualParts($name) {
    return preg_split('/[\s-]+/', $name);
  }


  /**
   * Returns a list of strings with the history of this person.
   * @return {Array} A list of history strings.
   */
  public function getHistory() {
    $s = mysql_query("SELECT * FROM people_history ".
                     "WHERE idperson={$this->id} ".
                     "ORDER BY time DESC, id DESC");

    $ret = array();
    while ($r = mysql_fetch_array($s)) {
      $ret[] = $r['what'];
    }
    return $ret;
  }


  /**
   * Returns a string representation of this object for debugging purposes.
   */
  public function toString() {
    return '(' . $this->displayName . ') ' . $this->name .
           ' [' . $this->id . ']';
  }

  /**
   * Returns the string used to identify a person by what they did.
   */
  public function getHistorySnippet() {
    $arr = array();
    // Fetch info from the history file.
    $s = mysql_query(
      "SELECT * FROM people_history
       WHERE idperson={$this->id}
       ORDER BY id DESC");
    while ($r = mysql_fetch_array($s)) {
      $url = $r['url'];
      $moduleTitle = $this->getShortTitleForWhat($r['what']);

      $arr[] = "<div class=what>$moduleTitle</div>";
    }
    return implode(", ", $arr);
  }


  public function getShortTitleForWhat($what) {
    $moduleTitle = '';
    switch($what) {
      case "cdep/2004":    $moduleTitle = "Deputat 2004-2008"; break;
      case "senat/2004":   $moduleTitle = "Senator 2004-2008"; break;
      case "results/2008": $moduleTitle = "Rezultate alegeri 2008"; break;
      case "alegeri/2008": $moduleTitle = "Alegeri parlamentare 2008"; break;
      case "euro/2009":    $moduleTitle = "Alegeri europarlamentare 2009"; break;
      case "catavencu/2008": $moduleTitle = "Candidații pătați Cațavencu 2008"; break;
      case "euro_parliament/2007": $moduleTitle = "Europarlamentar 2007-2009"; break;
      case "cdep/2008":    $moduleTitle = "Deputat 2008-2012"; break;
      case "senat/2008":   $moduleTitle = "Senator 2008-2012"; break;
      case "pres/2009":    $moduleTitle = "Candidat Președenție 2009"; break;
      case "gov/ro":       $moduleTitle = "Membru al guvernului"; break;
      case "video":        $moduleTitle = "Video"; break;
    }
    return $moduleTitle;
  }


  public function getLongTitleForWhat($what) {
    $moduleTitle = '';
    switch($what) {
      case "cdep/2004":    $moduleTitle = "Camera deputaților, 2004-2008"; break;
      case "cdep/2008":    $moduleTitle = "Camera deputaților, 2008-2012"; break;
      case "senat/2004":   $moduleTitle = "Senat, 2004-2008"; break;
      case "results/2008": $moduleTitle = "Rezultate alegeri, Noiembrie 2008"; break;
      case "alegeri/2008": $moduleTitle = "Candidat Parlamentare 2008"; break;
      case "euro/2009":    $moduleTitle = "Alegeri europarlamentare, 7 Iunie 2009"; break;
      case "catavencu/2008": $moduleTitle = "Candidații pătați, Academia Cațavencu, 2008"; break;
      case "resume":       $moduleTitle = "Curriculum vitae"; break;
      case "euro_parliament/2007": $moduleTitle = "Parlamentul European 2007-2009"; break;
      case "qvorum/2009":  $moduleTitle = "Studiul Qvorum despre europarlamentari"; break;
      case "senat/2008":   $moduleTitle = "Senator 2008 - 2012"; break;
      case "pres/2009":    $moduleTitle = "Alegeri prezindențiale 2009"; break;
      case "gov/ro":       $moduleTitle = "Membru al guvernului"; break;
      case "video":        $moduleTitle = "Video"; break;
      case "news":         $moduleTitle = "Știri pe larg"; break;
      case "news/expanded":$moduleTitle = "Știri pe larg"; break;
      case "person_declarations":$moduleTitle = "Declarații"; break;

    }
    return $moduleTitle;
  }

  /**
   * Returns the list of most recent news items that this person has been
   * mentioned in.
   * @param {number} count The max number of news items I need to retreive.
   */
  public function getMostRecentNewsItems($count, $start=0) {
    $s = mysql_query("
      SELECT a.id, a.title, a.link, a.time, a.place, a.source, a.photo
      FROM news_people AS p
      LEFT JOIN news_articles AS a ON p.idarticle = a.id
      WHERE p.idperson = {$this->id}
      ORDER BY a.time DESC
      LIMIT $start, $count");

    $news = array();
    while ($r = mysql_fetch_array($s)) {
      foreach ($this->allNames as $n) {
        $r['title'] = highlightStr($r['title'], $n);
      }
      foreach (split(" ", $this->displayName) as $n) {
        $r['title'] = highlightStr($r['title'], $n);
      }

      $r['people'] = $this->getPeopleForNewsId($r['id']);

      $news[] = $r;
    }
    return $news;
  }


  /**
   * Returns a list of the most recent declarations of this person.
   *
   * @param {String} $query The query we are looking for.
   * @param {Number} $start Where to start the results from.
   * @param {Number} $count The number of declarations that we need.
   * @param {Boolean} $full_text Whether we want to return just snippets or
   *     the full text for each of the results.
   * @param {String} $restrict The category of declarations that I am looking
   *     for. Acceptable values for this are 'all', 'important' or 'mine'.
   * @return {Array} The array of results.
   */
  public function searchDeclarations($query, $start, $count, $full_text,
                                     $restrict, $justDeclarationId=0) {
    $navigationalRestrict =
        $justDeclarationId ? "AND d.id={$justDeclarationId}" : "";

    if ($restrict == 'all') {
      $s = mysql_query("
        SELECT d.id, d.source, d.declaration, d.time
        FROM people_declarations AS d
        WHERE d.idperson = {$this->id} AND
            d.declaration LIKE '%{$query}%'
            {$navigationalRestrict}
        ORDER BY d.time DESC
        LIMIT {$start}, {$count}
      ");
    } else if ($restrict == 'important') {
      $sql = "
        SELECT d.id, h.source, d.declaration, d.time
        FROM people_declarations AS d
        LEFT JOIN people_declarations_highlights AS h ON h.source = d.source
        WHERE idperson = {$this->id}
        {$navigationalRestrict}
        GROUP BY h.source
        ORDER BY time DESC
        LIMIT {$start}, {$count}
      ";
      $s = mysql_query($sql);
    } else if ($restrict == 'mine') {
      $uid = is_user_logged_in() ? wp_get_current_user()->ID : 0;
      $sql = "
        SELECT d.id, h.source, d.declaration, d.time
        FROM people_declarations AS d
        LEFT JOIN people_declarations_highlights AS h ON h.source = d.source
        WHERE
            idperson = {$this->id} AND
            h.user_id={$uid}
            {$navigationalRestrict}
        GROUP BY h.source
        ORDER BY time DESC
        LIMIT {$start}, {$count}
      ";
      $s = mysql_query($sql);
    } else {
      return array();
    }

    $results = array();
    while ($r = mysql_fetch_array($s)) {
      if (!$r['source']) continue;
      // HACK: Because I know that the transcripts from cdep.ro have only this
      // one tag in them, I will manually replace it.
      $r['declaration'] = preg_replace('/<p align="justify">/', ' ',
                                       $r['declaration']);
      $r['declaration'] = strip_tags($r['declaration']);
      $r['declaration'] = stripslashes($r['declaration']);

      $r['snippet'] = $full_text ?
          $r['declaration'] : getSnippet($r['declaration'], $query, $full_text);

      $r['snippet'] = markDownTextBlock($r['snippet'], "");

      if ($query != '') {
        $r['snippet'] = highlightStr($r['snippet'], $query);
      }

      $results[] = $r;
    }

    return $results;
  }


  /**
   * Returns a list of ids for the people that show up in a news item.
   * TODO(vivi): Deduplicate this function and put it somewhere in a common
   * place.
   * @param {number} id The id of the news item.
   * @return Array The array of persons ids.
   */
  private function getPeopleForNewsId($id) {
    $s = mysql_query("
      SELECT idperson, display_name, name
      FROM news_people AS p
      LEFT JOIN people ON people.id = p.idperson
      WHERE idarticle=$id");
    $res = array();
    while($r = mysql_fetch_array($s)) {
      $r['name'] = str_replace(' ', '+', $r['name']);
      $res[] = $r;
    }
    return $res;
  }

  /**
   * Get the top associates of this person in the news.
   * @return unknown_type
   */
  public function getTopNewsAssociates($count) {
    $tstart = time() - 60 * 60 * 24 * 30; // 30 days ago
    $count = $count + 1;
    $s = mysql_query("
      SELECT count(*) AS cnt, assoc.idperson, people.display_name, people.name
      FROM news_people AS p
      LEFT JOIN news_people AS assoc ON assoc.idarticle = p.idarticle
      LEFT JOIN news_articles AS a ON a.id = p.idarticle
      LEFT JOIN people ON people.id = assoc.idperson
      WHERE p.idperson = {$this->id} AND time > {$tstart}
      GROUP BY assoc.idperson
      ORDER BY cnt DESC
      LIMIT 0, {$count}");
    $res = array();

    $total = mysql_fetch_array($s);
    $res[] = $total;

    while($r = mysql_fetch_array($s)) {
      $r['name'] = str_replace(' ', '+', $r['name']);
      $r['percent'] = 100 * $r['cnt'] / $total['cnt'];
      $res[] = $r;
    }
    return $res;
  }

  /**
   * Returns a list of qualifiers from the press for this person.
   * @param $count The limit.
   * @return Array
   */
  public function getNewsQualifiers($count) {
    $sql = "
      SELECT q.name, q.idperson, q.qualifier, count(*) as num,
             a.link
      FROM news_qualifiers AS q
      LEFT JOIN news_articles AS a ON a.id = q.idarticle
      WHERE q.approved = 1 AND idperson = {$this->id}
      GROUP BY concat(q.idperson, q.qualifier)
      ORDER BY num DESC
      LIMIT 0, $count";
    $s = mysql_query($sql);
    $res = array();
    while ($r = mysql_fetch_array($s)) {
      $res[] = $r;
    }
    return $res;
  }

  /**
   * Returns a list of most recent videos of this person.
   * @param $count
   * @return unknown_type
   */
  public function getMostRecentVideos($count) {
    $s = mysql_query("
      SELECT v.idperson, v.thumb, v.title, v.player_url, v.time,
          v.duration, v.watch_url
      FROM yt_videos AS v
      WHERE v.approved = 1 AND idperson={$this->id}
      ORDER BY time DESC
      LIMIT 0, $count
      ");

    $res = array();
    while($r = mysql_fetch_array($s)) {
      $r['title'] = stripslashes($r['title']);
      $res[] = $r;
    }

    return $res;
  }


  /**
   * Given an id, looks on disk for the tiny picture of this person.
   * Returns the default non-photo for people that don't have a tiny picture.
   * @param $id
   * @return unknown_type
   */
  public function getTinyImgUrl() {
    $img = "images/people_tiny/{$this->id}.jpg";
    if (is_file($img)) {
      $fname = "images/people_tiny/{$this->id}.jpg";
      $count = 1;
      // Get the most recent file we have for this person.
      while (is_file($fname)) {
        $img = $fname;
        $fname = "images/people_tiny/{$this->id}_{$count}.jpg";
        $count++;
      }
    } else {
      return "images/tiny_person.jpg";
    }
    return $img;
  }


  /**
   * Returns the name of the college that this person was a candidate in, if
   * indeed they were a candidate. Returns NULL otherwise.
   */
  public function getCollegeName() {
    $sql = "SELECT colegiu FROM results_2008 WHERE idperson = {$this->id}";
    $s = mysql_query($sql);
    if ($r = mysql_fetch_array($s)) {
     return $r['colegiu'];
    }

    return NULL;
  }

  /**
   * Returns a list of contact details (s.a. website and email) of this
   * person imported from Agenda Parlamentarilor (http://agenda.grep.ro).
   */
  public function getContactDetails() {
    $s = mysql_query("
      SELECT attribute, value
      FROM people_facts
      WHERE idperson = {$this->id}
      AND attribute LIKE 'contact/%'");

    // Ordered by the probability to have that contact detail and its length
    $details = array(
      "website"  => array(),
      "email"    => array(),
      "phone"    => array(),
      "address"  => array(),
      "facebook" => array(),
      "twitter"  => array()
    );

    while ($r = mysql_fetch_array($s)) {
      // Save recognized detail types out of attributes in db
      $dkey = str_replace("contact/", "", $r['attribute']);
      if (!array_key_exists($dkey, $details)) continue;

      // There can be more than one detail of a type
      $dval = $r['value'];
      array_push($details[$dkey], $dval);
    }

    return $details;
  }
}
?>
