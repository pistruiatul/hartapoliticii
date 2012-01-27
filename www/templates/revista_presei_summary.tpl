{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=350 valign="top">
    
      <p class="smalltitle">
        <strong>Cei mai mediatizați zece politicieni (mediafax)</strong><br>
      </p>
        {* This section is almost identical to news_buzz_top_people. *}
        {include file="revista_presei_top_mentions_politicians.tpl"}
      
    </td>

    <td valign="top">
    </div>
      <p class="smalltitle"><strong>{$MOST_RECENT_NEWS_ABOUT} 
        (via <a href="http://www.mediafax.ro">mediafax.ro</a>)
      </strong></p>
      <div class="medium">
      {include file="news_buzz_latest_news.tpl"}
    </td>
  </tr>
</table>
