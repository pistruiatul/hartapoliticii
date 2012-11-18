{* Smarty *}

{section name=n2 loop=$news}
{strip}
  <span class="small gray"><em>
    {$news[n2].time|date_format:"%d&nbsp;%b"}
  </em>
  </span>&nbsp;
    
  <a href="{$news[n2].link}" class="black_link">
     {$news[n2].title|stripslashes}&nbsp;
     <img src="images/popout_icon.gif" border="0" width="12" height="12">
  </a><br>
{/strip}
{/section}