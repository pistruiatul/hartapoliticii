<table width=970 cellspacing=15>
  <tr>
    <td width=950 valign="top" align="center">
    {if $restrict}
      <div class="college_search_button"
           style="width:680px; text-align: left; margin-bottom: 20px">
        {$restrict}
      </div>
    {/if}

    {* ------------------------------------------------------------------*}
    {* The main news section from the front page. *}
    <div style="width: 700px" class="bigger_news_list">
      {include file="news_list_wide.tpl" news=$news}
    </div>
  </td>
  </tr>
</table>
