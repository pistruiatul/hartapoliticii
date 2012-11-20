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
      {include file="news_list_wide.tpl" news=$news}
  </div>
  {if $prev>=0}
	  <a href="?name={$name}&exp=news&start={$prev}">prev page</a>
	  &nbsp;/&nbsp;
  {/if}
  <a href="?name={$name}&exp=news&start={$next}">next page</a>
{/if}
