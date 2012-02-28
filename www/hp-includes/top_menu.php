
<?php
$t = new Smarty();
$t->assign('logged_in', is_user_logged_in());

if (is_user_logged_in()) {
	global $current_user;
	$t->assign('user_login', $current_user->user_login);

  if(function_exists('get_logout_url')) {
    $t->assign('logout_url', getLogoutUrl('/'));
  } else {
    $t->assign('logout_url', wp_logout_url('/'));
  }
}

$t->assign('escaped_query', htmlspecialchars($query));
$t->assign('cid', $cid);
$t->assign('title', $title);

$t->assign('site_path', '/');

$t->display('common_top_menu.tpl');
?>
