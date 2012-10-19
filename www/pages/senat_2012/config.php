<?php

$subpages = array(
  array(
    "link" => "Alegeri 2012",
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
