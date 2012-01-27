{* Smarty *}

<div class="login_bar">
  <div>
	{if $logged_in}
	  Autentificat ca <b><a href="?cid=profile">{$user_login}</a></b> /
	  <a href="{$logout_url}">Log out</a>
	{else}
	  <a href="wp-login.php?action=login">Autentificare</a>
    / <a href="wp-login.php?action=register">Înscriere</a>
	{/if}
  / <a href="wp-login.php?action=register">De ce?</a>
	</div>
</div>
