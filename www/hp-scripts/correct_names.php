<?

// This is a one-off script that loads a CSV with personId and name and
// replaces the name in the people database with this new name.
include("../_top.php");

// Load the file.
$file = fopen("/tmp/HP.people-fixed.csv", "r");
while (!feof($file)) {
  $line = fgets($file);

  // The id is first, the name is second. The name is surrounded by quotes.
  $row = explode(",", $line);

  $sql = "UPDATE people SET display_name = {$row[1]} WHERE id={$row[0]}";

  mysql_query($sql);

  echo $line . '<br>';
  echo $sql . '<br>';
}


include("../_bottom.php");
?>
