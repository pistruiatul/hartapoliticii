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

  /**
   * Constructs a Party object based on the id passed in as a parameter. The id
   * is the same ID that the party has in the database.
   * @param $id The id of the party in the database.
   * @return {Party}
   */
  public function Party($id) {
    $this->id = $id;

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
  }
}
?>
