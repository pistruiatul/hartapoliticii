<?
include("../_top.php");

/**
 * Inserts the number of allocated seats in the Chamber of Deputies per party
 * for each county.
 */
function insertAllocatedDeputySeats($county, $room, $s1, $s2, $s3, $s4) {
  mysql_query("insert into results_2008_allocated(judet, partid, numar, room) " . 
              "values('$county', 'PARTIDUL DEMOCRAT LIBERAL', $s1, '$room')");

  mysql_query("insert into results_2008_allocated(judet, partid, numar, room) " . 
              "values('$county', 'ALIANTA POLITICA PARTIDUL SOCIAL DEMOCRAT + PARTIDUL CONSERVATOR', $s2, '$room')");

  mysql_query("insert into results_2008_allocated(judet, partid, numar, room) " . 
              "values('$county', 'PARTIDUL NATIONAL LIBERAL', $s3, '$room')");

  mysql_query("insert into results_2008_allocated(judet, partid, numar, room) " . 
              "values('$county', 'UNIUNEA DEMOCRATA MAGHIARA DIN ROMÂNIA', $s4, '$room')");

  echo $county . " inserted!";
}

if ($_GET['judet'] != "") {
  insertAllocatedDeputySeats($_GET['judet'], "S", $_GET['s1'], $_GET['s2'], $_GET['s3'], $_GET['s4']);
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
  <form action="" method=GET>
    S - Judet: <input name=judet size=15></input>
    PDL: <input name=s1 size=5></input>
    PSD+PC: <input name=s2 size=5></input>
    PNL: <input name=s3 size=5></input>
    UDMR: <input name=s4 size=5></input>
    <input type=submit value=go>
  </form>
</body>
</html>
<?
include("../_bottom.php");
?>