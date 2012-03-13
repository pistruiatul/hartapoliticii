<?php
  $title = "Camera Deputaților, mandatul 2004-2008";
  include('header.php');
  include('hp-includes/string_utils.php');
?>

<?php
  printWarning();
?>
<div class="plaintext">Pentru prezență, am luat în calcul cele 3593 de voturi electronice din camera deputaților exercitate
între Februarie 2006 și Noiembrie 2008. Dacă numărul de voturi nu e menționat în dreptul unui deputat, înseamnă că procentul e
calculat din numărul total de voturi din această perioadă.
</div>

<?php

showPresencePercentage($_GET['sort'], $_GET['order']);
?>
