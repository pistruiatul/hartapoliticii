{* Smarty *}

<div class="top_bar">
	<div class="numedeputattitlu">{$name}</div>
	{section name=c loop=$crumbs}
	  {strip}
	  &nbsp;&gt;&nbsp;<a href="{$crumbs[c].href}">{$crumbs[c].name}</a>
	  {/strip}
	{/section}

</div>
