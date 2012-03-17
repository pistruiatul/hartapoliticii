<?php
/**
 * Returns the link of a declaration from it's ID in the people_declarations
 * table.
 *
 * @param $declarationId
 * @return {String} The link.
 */
function getDeclarationLink($declarationId) {
  $sql = "SELECT source FROM people_declarations WHERE id=${declarationId}";
  $s = mysql_query($sql);
  if ($r = mysql_fetch_array($s)) {
    return $r['source'];
  }
  return NULL;
}


function getPersonId($declarationId) {
  $sql = "SELECT idperson FROM people_declarations WHERE id=${declarationId}";
  $s = mysql_query($sql);
  if ($r = mysql_fetch_array($s)) {
    return $r['idperson'];
  }
  return NULL;
}


/**
 * Used to show the most recent highlighted passages on the front page.
 * @return {Array}
 */
function getMostRecentDeclarations() {
  $sql = "
      SELECT h.source, h.content, d.time, p.display_name, p.id AS idperson,
          p.name
      FROM people_declarations_highlights AS h
      LEFT JOIN people_declarations AS d ON d.source = h.source
      LEFT JOIN people AS p ON d.idperson = p.id
      ORDER BY h.id DESC
      LIMIT 0, 5";
  $s = mysql_query($sql);
  $results = array();
  while ($r = mysql_fetch_array($s)) {
    $r['name'] = str_replace(' ', '+', $r['name']);
    $r['content'] = stripslashes($r['content']);
    $results[] = $r;
  }
  return $results;
}


function searchDeclarations($query) {
  $sql = "
      SELECT d.id, d.source, d.declaration, d.time, count(*) as cnt,
          people.display_name, people.name, d.idperson
      FROM people_declarations AS d
      LEFT JOIN people ON people.id = d.idperson
      WHERE d.declaration LIKE '%{$query}%'
      GROUP BY d.idperson
      ORDER BY cnt DESC
      LIMIT 0, 20";

  $s = mysql_query($sql);
  $results = array();
  while ($r = mysql_fetch_array($s)) {
    $r['name'] = str_replace(' ', '+', $r['name']);
    $results[] = $r;
  }
  return $results;
}

?>
