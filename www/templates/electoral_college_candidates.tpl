<table width=400 cellpadding=2>
  {section name=c loop=$candidates}

  <tr class="candidaterow" {if !$compact}height=50{/if}>
    <td valign="top">
      <img src="{$candidates[c].tiny_img_url}"
           {if !$compact}height="40"{/if}>
    </td>
    <td width="300" valign="top">
      <div style="margin-top:10px;{if !$compact}font-size: 18px;{/if}">
      <a href="?name={$candidates[c].name|replace:' ':'+'}">
        {$candidates[c].display_name}</a>
      {if !$compact}
      <div class="history_snippet">
        {$candidates[c].history_snippet}
      </div>
      {/if}
      </div>
    </td>
    <td width="100" valign="top">
      <img src="/images/parties/sigla_{$candidates[c].party|lower}50x50.png"
           width="30" height="30" valign="middle">

      {$candidates[c].party}</td>
  </tr>
  {/section}
</table>

