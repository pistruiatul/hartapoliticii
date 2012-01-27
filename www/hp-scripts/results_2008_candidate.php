<?
include("../_top.php");

if ($_GET['original']) {
  $orig = $_GET['original'];
  $new = $_GET['new'];

  echo $orig . ' -> ' . $new;
  $soq = "select id from results_2008_candidates where nume LIKE '$orig'";

  $so = mysql_query($soq);
  $sn = mysql_query("select id from results_2008_candidates where nume LIKE '$new'");

  if ($ro = mysql_fetch_array($so)) {
    if ($rn = mysql_fetch_array($sn)) {
      $origid = $ro['id'];
      $newid = $rn['id'];

      if ($origid != $newid) {
        echo "<br>" . $origid . " -> " . $newid;

        mysql_query("update results_2008 set nume='$new', idcandidat=$newid where idcandidat=$origid");
        mysql_query("update results_2008_agg set winnerid='$newid' where winnerid=$origid");
        mysql_query("update results_2008_agg set runnerupid='$newid' where runnerupid=$origid");

        // Delete the old candidate
        mysql_query("delete from results_2008_candidates where id=$origid");
      } else {
        mysql_query("update results_2008_candidates set nume='$new' where id=$origid");
      }
    } else {
      $origid = $ro['id'];
      // just update the name
      mysql_query("update results_2008_candidates set nume='$new' where id=$origid");
    }
  } else {
    echo "<br>Original not found!";
  }
}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<form action="" method="GET">
  <input type=text size=40 name=original></input>
  <input type=text size=40 name=new></input>
  <input type=submit value=Go>
</form>
</html>
<?
include("../_bottom.php");
?>