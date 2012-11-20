{* Smarty *}

<div class="medium">

{if $restrict}
  <div class="college_search_button"
       style="width:680px; text-align: left; margin-bottom: 20px">
    {$restrict}
  </div>
{/if}

<table cellspacing=2 cellpadding=2 class="recent_news" style="width: 100%;">
  {section name=n loop=$news}
    <tr>
    <td valign="top" align="center" width="37">
      <a onclick="ec.voteArticle('{$news[n].id}', 1);" style="cursor:pointer;">
        <img src="/images/bullet_arrow_up.png"></a>

      <div class="ugc_score" id="article_score_{$news[n].id}">{$news[n].votes}</div>

      <a onclick="ec.voteArticle('{$news[n].id}', -1);" style="cursor:pointer">
      <img src="/images/bullet_arrow_down.png">
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
          <span class="ugc_source">{$news[n].source}</span>
        </nobr>

        {include file="news_list_mentions_block.tpl"
            people=$news[n].people
            news_id=$news[n].id
            above_six=$news[n].above_six}

        <div class="ugc_link_status">
          {$news[n].time|date_format:"%e %b %Y, "}
          {$news[n].time|date_format:"%l%p"|replace:"PM":"pm"|replace:"AM":"am"}
           · link adăugat de <span style="color:blueviolet">{$news[n].user_name}</span>
           · <a href="/?cid=comunitate&id={$news[n].id}#disqus_thread" class="comments">comentarii</a>
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


{literal}
<script type="text/javascript">
/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
var disqus_shortname = 'hartapoliticii'; // required: replace example with your forum shortname

/* * * DON'T EDIT BELOW THIS LINE * * */
(function () {
    var s = document.createElement('script'); s.async = true;
    s.type = 'text/javascript';
    s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());
</script>
{/literal}