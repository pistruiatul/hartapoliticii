{* Smarty *}

<a name="{$NEWS_CODE}"></a>
<table width=970 cellspacing=15>
  <tr>
    <td width=350 valign="top">
    
      <p class="smalltitle">
        <img src="images/logo_news_{$NEWS_CODE|replace:' ':'_'}.jpg" align="absmiddle">
        <strong>{$NEWSPAPER_TITLE}</strong>
      </p>
        {* This section is almost identical to news_buzz_top_people. *}
        {include file="revista_presei_top_mentions_politicians.tpl"}
    </td>

    <td valign="top">
    </div>
      <p class="smalltitle"><strong>{$MOST_RECENT_NEWS_ABOUT}</strong></p>
      <div class="medium">
      {include file="news_list_wide.tpl" news=$news}
    </td>
  </tr>
</table>
