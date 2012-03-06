{* Smarty *}

<div class="login_bar">
  <div>
	{if $logged_in}
	  Autentificat ca <b><a href="?cid=profile">{$user_login}</a></b> /
	  <a href="{$logout_url}">Log out</a>
	{else}
	  <a href="wp-login.php?action=login">Autentificare</a>
	{/if}
  /
    <a href="{$site_path}?cid=6" class="{if $cid==6}black_link{/if}">Despre site</a></div>
	</div>
</div>
