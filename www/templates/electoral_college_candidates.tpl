<table width=585 cellpadding=2>
  {section name=c loop=$candidates}
  {if $smarty.section.c.index % 2 == 0}
    <tr class="candidaterow">
  {/if}
    <td valign="top" width=60 align="right">
      <img src="{$candidates[c].tiny_img_url}"
           {if !$compact}height="40"{/if}>
    </td>
    <td width="300" valign="top">
      <div style="float:right; margin:13px 10px 0 0;">
        <a href="{$candidates[c].source}">
          <img src="/images/popout_icon_light.gif"></a>
      </div>

      <div style="margin-top:10px;{if !$compact}font-size: 18px;{/if}">
      <a href="?name={$candidates[c].name|replace:' ':'+'}">
        {$candidates[c].display_name}</a>

      <br><b>{$candidates[c].voturi}</b> voturi

      {if !$compact}
      <div class="history_snippet">
        {$candidates[c].history_snippet}
      </div>
      {/if}
      </div>
    </td>
    <td width="60" valign="top" class="party_cell"
            {if $smarty.section.c.index % 2 != 1}
          style="border-right: 1px solid #DDD;"
        {/if}>
      <div style="margin:5px 5px 0 0;">
      <img src="/images/parties/sigla_{$candidates[c].party_logo|lower}50x50.png"
           width="30" height="30" valign="middle">
      </div></td>
  {/section}
</table>

