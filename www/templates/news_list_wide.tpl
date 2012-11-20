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
            {$news[n].title|stripslashes}
          </a>
        </div>&nbsp;
        <nobr>
        <img src="images/popout_icon.gif" border="0" width="12"
             height="12" hspace="5">
          <span class="gray medium">{$news[n].source}</span>
        </nobr>

        {include file="news_list_mentions_block.tpl" people=$news[n].people
            news_id=$news[n].id
            above_seven=$news[n].above_seven}

      </div>
    </td>

    {if $news[n].photo != ""}
      {* If this article has a photo, put a div here with that photo *}
      <td width="100" valign="top">
        <div class="container">
          <div class="photo">
            <img src="{$news[n].photo}{if ($news[n].source == 'hotnews' || $news[n].source == 'mediafax') }?width=100{/if}" width="100">
          </div>
        </div>
      </td>
    {/if}

    </tr>
  {/section}
</table>
</div>
