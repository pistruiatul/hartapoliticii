<?php
include ('_top.php');
include ('functions.php');
include ('functions_election_stats.php');

if (!is_user_logged_in() && $_GET['cid'] == 'profile') {
  header('Location: /wp-login.php');
}

if ($_GET['cid'] && $_GET['cid'] == 9) {
  $s = mysql_query("SELECT name FROM people WHERE id = {$_GET['id']}");
  if ($r = mysql_fetch_array($s)) {
    $om = str_replace(' ', '+', $r['name']);
    header("Location: /?name={$om}");
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta property="fb:admins" content="521485175" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="styles.css?v=<? echo md5_file('styles.css');?>" />
  <script src="js/swfobject/swfobject.js" type="text/javascript"></script>

<?php
// Include or not include some scripts, depending on whether this is localhost
// or not.
if ($_SERVER['SERVER_NAME'] == 'localhost' ||
    $_SERVER['SERVER_NAME'] == 'mini.local') { ?>
  <script src="politica_localhost.js" type="text/javascript"></script>
<? } ?>
<script src="politica.js?v=<? echo md5_file('politica.js');?>"
        type="text/javascript"></script>

<?php if ($_SERVER['SERVER_NAME'] != 'localhost' &&
          $_SERVER['SERVER_NAME'] != 'swiss.local') { ?>
  <script type="text/javascript">
  var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
  document.write(unescape("%3Cscript src='" + gaJsHost +
    "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
  </script>
  <script type="text/javascript">
  try {
  var pageTracker = _gat._getTracker("UA-71349-2");
  pageTracker._trackPageview();
  } catch(err) {}</script>
<?php }?>

<?php
// See if this was a search result and update the search logs.
if ($_GET['ssid'] != '') {
  $ssid = (int) $_GET['ssid'];
  $ssp = (int) $_GET['ssp'];
  mysql_query(
    "UPDATE log_searches SET found = $ssp WHERE found = -1 AND id=$ssid");
}

$cid = $_GET['cid'] ? $_GET['cid'] : 1000;
if ($_GET['name']) {
  $cid = 9;
}

switch ($cid) {
  // The home page.
  case 1000: include('pages/home_page/home_page.php'); break;

  case 1: include('cam_dep.php'); break;
  case 3: include('senat.php'); break;
  case 2: case 4: include('fiecarevot.php'); break;
  case 5: include('utile.php'); break;
  case 6: include('despre.php'); break;

  case 9: include('person.php'); break;
  case 'search': include('hp-includes/search.php'); break;

  // Alegeri europarlamentare 2009.
  case 10: include('pages/euro_2009/europarlament_page.php'); break;

  // Senat si camerat deputatilor 2008 - 2012.
  case 11: include('pages/cdep_2008/cdep_2008_page.php'); break;
  case 12: include('pages/senat_2008/senat_2008_page.php'); break;

  // Alegeri prezidentiale 2009.
  case 13: include('pages/pres_2009/page.php'); break;

  // Sumar general de presa, probabil homepage.
  case 14: include('pages/revista_presei/revista_presei_page.php'); break;

  // A logged in user's account.
  case 'profile': include('my_account.php'); break;

  // A logged in user's account.
  case 15: include('pages/compass/show_tag.php'); break;
  case 16: break; // The blog, just reserve it.

  case 17: include('party.php'); break;
}

$t = new Smarty();
$t->display('common_footer.tpl');

?>
<br/>

<div id="playerwrapper" style="position:fixed;">
</div>

</body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=205183855930";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

</html>
<?php
include('_bottom.php');
?>
