<title><? echo $title ?> - Harta politicii din România</title>
</head>
<body onload="loadHandler();">
<center>
<?php include_once('hp-includes/top_menu.php')?>
<table width="970" cellspacing=0 cellpadding=0><td>

<?if (!$nowarning) {?>
  <div class="plaintext">
  <? if ($top_warning) {
    echo $top_warning;
  } else {?>
    <b><font color="red">Atenție</font></b> Este posibil ca în aceste date să
    se fi strecurat erori, există anumite reguli pe care nu le-am luat în
    calcul (miniștri sunt scuzați de la vot, cei plecați în delegații asemenea).
    Nu folosiți aceste date decât cu caracter orientativ!
  <?}?>
  </div>
<?}?>
