<?

$subpages = array(
  array(
    "link" => "Sumar",
    "page" => "summary.php"
  )
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