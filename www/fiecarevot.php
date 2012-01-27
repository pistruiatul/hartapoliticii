<?
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: http://www.vivi.ro/politica');
}

if ($_GET['room'] == 'senat') {
  $title = "Senat, Rezultate alegeri 2008";
} else {
  $title = "Camera Deputaților, Rezultate alegeri 2008";
}
$top_warning = "<b><font color=red>Atenție</font></b>: ".
  "Este posibil ca în aceste date să se fi strecurat erori neintenționate.";

include('header.php');
?>
<div class=plaintext>
  Pentru fiecare loc din
  <? echo $_GET['room'] == 'senat' ? 'Senat' : 'Camera Deputaților'; ?>,
  care este numărul minim de voturi care ar fi schimbat câștigătorul?

  <br>Mai multe explicații
  <a href="http://www.vivi.ro/blog/?p=1340">găsiți aici</a>.
</div>

<?
showVotesThatCount($_GET['order'], $_GET['room']);
?>
