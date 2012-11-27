<table width=950 cellpadding=2>
  {section name=c loop=$candidates}
    {if $smarty.section.c.index % 3 == 0}
    <tr class="candidaterow">
    {/if}

    <td valign="top" width="50" align="right">
      <img src="{$candidates[c].tiny_img_url}"
           {if !$compact}height="40"{/if}>
    </td>
    <td width="330" valign="top">
      <div style="float:right; margin:13px 10px 0 0;">
        <a href="{$candidates[c].source}">
          <img src="/images/popout_icon_light.gif"></a>
      </div>

      <div style="margin-top:10px;{if !$compact}font-size: 16px;{/if}">
      <a href="?name={$candidates[c].name|replace:' ':'+'}">
        {$candidates[c].display_name}</a>
      {if !$compact}
      <div class="history_snippet small">
        {$candidates[c].history_snippet}
      </div>
      {/if}
      </div>
    </td>
    <td width="100" valign="top" class="party_cell" align="center"
        {if $smarty.section.c.index % 3 != 2}
          style="border-right: 1px solid #DDD;"
        {/if}>
      <div style="margin:5px 10px 0 0;" class="medium gray">
      <img src="/images/parties/sigla_{$candidates[c].party_logo|lower}50x50.png"
           width="30" height="30" valign="middle"><br>
      <nobr>{$candidates[c].displayed_party_name}</nobr></div>
    </td>

  {/section}
</table>

