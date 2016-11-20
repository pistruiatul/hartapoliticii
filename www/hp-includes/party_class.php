<?php
include_once('string_utils.php');

/**
 * @fileoverview A class identifying a party. This class will contain utilities
 * for getting various information about a party from the database.
 */
class Party {
  /** The short name of this party. */
  public $name;

  /** The long name of this party. */
  public $longName;

  /** The id of this party in the database. */
  public $id;

  /** The candidate lists for 2016. Shouldn't be here, but as a hack this works and
   * I'm sorry future person that will have to deal with this by right now I just need
   * to hack this sorry. */
  public $lists;

  /**
   * Constructs a Party object based on the id passed in as a parameter. The id
   * is the same ID that the party has in the database.
   * @param $id The id of the party in the database.
   * @return {Party}
   */
  public function Party($id) {
    $this->id = $id;
    $this->lists = array();

    $s = mysql_query("SELECT name, long_name FROM parties WHERE id={$id}");

    if ($s && $r = mysql_fetch_array($s)) {
      $this->name = strtoupper($r['name']);
      $this->longName = ucwords($r['long_name']);
    }
  }

  /**
   * Returns a list of strings with the history of this party.
   * @return {Array} A list of history strings.
   */
  public function getHistory() {
    $s = mysql_query("SELECT what FROM parties_modules ".
                     "WHERE idparty={$this->id} ".
                     "ORDER BY time DESC, id DESC");

    $ret = array();
    while ($r = mysql_fetch_array($s)) {
      $ret[] = $r['what'];
    }
    return $ret;
  }

  /**
   * Returns a URL with the photo of this party.
   * @return {string}
   */
  public function getLogo() {
    $s = mysql_query("
      SELECT value FROM parties_facts
      WHERE idparty={$this->id} AND attribute='logo'
    ");
    if ($r = mysql_fetch_array($s)) {
      return $r['value'];
    }
    // TODO(vivi): Replace this with a party photo.
    return '/images/face2.jpg';
  }

  public function getLongTitleForWhat($what) {
    switch($what) {
      case 'cdep2008': return 'Camera Deputaților 2008-2012';
      case 'senat2008': return 'Senat 2008-2012';
    }
    return '';
  }

  /**
   * Returns the list of candidates and their results for this college. For now
   * this method is not generic at all, hence the very specific name.
   *
   * TODO(vivi): Refactor and generalize this.
   *
   * @param {string} $college The college name needs to be in the form of
   *     "S1 Alba" or "D3 Prahova". Capitalization is important.
   */
  function get2016List($college) {
    $sql =
        "SELECT people.id as id, people.display_name, people.name, results.idcandidat, ".
        "history.url as source, " .
        "results.voturi " .
        "FROM results_2016 AS results ".
        "LEFT JOIN people ON people.id = results.idperson ".
        "LEFT JOIN people_history AS history ".
        "ON people.id = history.idperson AND history.what = 'results/2016' ".
        "WHERE colegiu = '{$college}' AND results.idpartid = {$this->id} " .
        "ORDER BY results.idcandidat ASC";
    $s = mysql_query($sql);

    $candidates = array();

    while ($r = mysql_fetch_array($s)) {
      $person = new Person();
      $person->name = $r['name'];
      $person->id = $r['id'];

      $person_object = $r;
      $person_object['tiny_img_url'] = getTinyImgUrl($r['id']);
      $person_object['history_snippet'] =
          $person->getHistorySnippet(array('results/2016'), true);

      $person_object["displayed_party_name"] = $r["party"];
      $person_object["party_logo"] = $r["party"];

      $candidates[] = $person_object;
    }
    return $candidates;
  }

}
?>
