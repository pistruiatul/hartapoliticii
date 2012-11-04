{* Smarty *}

{if count($assoc) > 0}
  <b>În ultima lună, cel mai des menționat împreună cu</b>

  <div style="padding-left:10px">
    <table cellspacing=2 cellpadding=2 class="recent_news">
      {section name=a loop=$assoc start=1}
      {strip}
        <tr>
        <td valign="top">
          <span class="small green_link">
            <a href="?name={$assoc[a].name}">
              {$assoc[a].display_name}
            </a>
          </span>
        </td>
        <td width="300">
          <div style="background:#44AA44;
               margin-right:10px;
               display:inline-block;
               width:{$assoc[a].percent|string_format:"%.0f"}%;
               height:5px"></div>
          <span class="small">{$assoc[a].cnt}</span>
        </td>
        <td></td>
        </tr>
      {/strip}
      {/section}

      <tr>
      <td valign="top">
        <span class="small"><b>Total</b>:</span>
      </td>
      <td width="300">
        <div style="background:#AA4444;
             margin-right:10px;
             display:inline-block;
             width:100%;
             height:5px"></div>
      </td><td>
        <span class="small">{$total_news}</span>
      </td>
      </tr>

    </table>
  </div>
{/if}

{if count($news) > 0}
  <br>
  <b>Lista de știri</b>
  <div style="padding-left:10px">
    <table cellspacing=2 cellpadding=2 class="recent_news">
      {section name=n loop=$news}
      {strip}
        <tr>
        <td valign="top">
          <span class="small">
            <em>{$news[n].time|date_format:"%e&nbsp;%b"}</em><br>
            <em><span class="light_gray">{$news[n].time|date_format:"%Y"}</span></em><br>
            <em><span class="light_gray">{$news[n].time|date_format:"%l%p"|replace:"PM":"pm"|replace:"AM":"am"}</span></em>

          </span>
        </td>

        {if $news[n].photo != ""}
          <td valign="top">
        {else}
          <td colspan="2" valign="top">
        {/if}

         <div class="medium recent_news_title">
           <span class="black_link">
             <a href="{$news[n].link}">
               {$news[n].title}&nbsp;
               <nobr>
                 <img src="images/popout_icon.gif" border="0" width="12" height="12"
                      hspace="5">
                 <span class="gray medium">{$news[n].source}</span>
               </nobr>
             </a>
           </span>
           <br>

           <div class="recent_news_block">
             {include file="news_list_mentions_block.tpl" people=$news[n].people
                news_id=$news[n].id
                above_seven=$news[n].above_seven}
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
      {/strip}
      {/section}
    </table>
  </div>
  {if $prev>=0}
	  <a href="?name={$name}&exp=news&start={$prev}">prev page</a>
	  &nbsp;/&nbsp;
  {/if}
  <a href="?name={$name}&exp=news&start={$next}">next page</a>
{/if}
