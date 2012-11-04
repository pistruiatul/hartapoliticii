{* Smarty *}

<div class="medium">
<table cellspacing=2 cellpadding=2 class="recent_news">
  {section name=n loop=$news}
    <tr>
    <td valign="top">
      <span class="small">
        <em>{$news[n].time|date_format:"%e&nbsp;%b"}</em><br>
        <em><span class="light_gray">{$news[n].time|date_format:"%l%p"|replace:"PM":"pm"|replace:"AM":"am"}</span></em>
      </span>
    </td>

    {if $news[n].photo != ""}
      <td valign="top">
    {else}
      <td colspan="2" valign="top">
    {/if}

      <div class="recent_news_block">
        <div class="recent_news_title black_link">
          <a href="{$news[n].link}" target="_blank">
            {$news[n].title}
          </a>
        </div>&nbsp;
        <nobr>
        <img src="images/popout_icon.gif" border="0" width="12"
             height="12" hspace="5">
          <span class="gray medium">{$news[n].source}</span>
        </nobr>

        <div class="mentions_block">
          {section name=x loop=$news[n].people}
          {strip}
            <div class="news_list_mention green_link {if $news[n].people[x].following}following{/if}{if $news[n].people[x].highlight} highlight{/if}">
              <a href="?name={$news[n].people[x].name}">
                {$news[n].people[x].display_name}
              </a>
            </div>
          {/strip}
          {/section}
        </div>
      </div>
    </td>

    {if $news[n].photo != ""}
      {* If this article has a photo, put a div here with that photo *}
      <td width="100" valign="top">
        <div class="container">
          <div class="photo">
            <img src="{$news[n].photo}?width=100">
          </div>
        </div>
      </td>
    {/if}

    </tr>
  {/section}
</table>
</div>