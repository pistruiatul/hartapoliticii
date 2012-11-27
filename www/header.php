<title><?php echo $title ?> - Harta Politicii din România</title>
<script type="text/javascript">
  var uid = <?php echo (isSet($uid) ? $uid : 0) ?>;
  var personId = <?php echo (isSet($person) ? $person->id : 0) ?>;
</script>
</head>
<body onload="loadHandler();">
<center>
<?php include_once('hp-includes/top_menu.php')?>
<table width="970" cellspacing=0 cellpadding=0><td>
