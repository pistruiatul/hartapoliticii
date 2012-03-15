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


?>
