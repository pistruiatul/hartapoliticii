<?

$subpages = array(
  array(
    "link" => "Sumar",
    "page" => "summary.php"
  ),
  array(
    "link" => "Lista deputati",
    "page" => "all_deputies.php"
  ),
  array(
    "link" => "Lista voturi",
    "page" => "all_votes.php"
  ),
  array(
    "link" => "Alegeri 2008",
    "page" => "elections.php"
  ),
);

function getSidFor($fileName) {
  global $subpages;

  $count = 0;
  foreach ($subpages as $page) {
    if ($page['page'] == $fileName) {
      return $count;
    }
    $count++;
  }
  return -1;
}

?>
