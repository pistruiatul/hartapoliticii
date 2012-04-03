<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) header('Location: /');

$title = "Senat, Rezultate alegeri 2008";

$top_warning = "<b><font color=red>Atenție</font></b>: ".
  "Este posibil ca în aceste date să se fi strecurat erori neintenționate.";

?>
<div class=plaintext>
  Pentru fiecare loc din Senat,
  care este numărul minim de voturi care ar fi schimbat câștigătorul?

  <br>Mai multe explicații
  <a href="http://www.vivi.ro/blog/?p=1340">găsiți aici</a>.
</div>

<?php
showVotesThatCount($_GET['order'], 'senat');
?>
