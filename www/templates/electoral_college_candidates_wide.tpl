<table width=950 cellpadding=2>
  {section name=c loop=$candidates}
    {if $candidates[c].minoritati == 0}
      <tr class="candidaterow">
    {else}
      <tr class="candidaterow minoritati_2012" name="minoritati_2012">
    {/if}

    <td valign="top" width="50" align="right">
      <img src="{$candidates[c].tiny_img_url}"
           {if !$compact}height="40"{/if}>
    </td>
    <td width="530" valign="top">
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
    <td width="100" valign="top" class="party_cell" align="center">
      <div style="margin:5px 10px 0 0;" class="medium gray">
      <img src="/images/parties/sigla_{$candidates[c].party_logo|lower}50x50.png"
           width="30" height="30" valign="middle"><br>
      <nobr>{$candidates[c].displayed_party_name}</nobr></div>
    </td>
    <td valign="top" class="votes_cell" align="left">
      <b>{$candidates[c].voturi}</b> voturi
    </td>

  {/section}

  {* only show this if there actually are minorities candidates around *}
  {if $show_minorities_link}
  <tr class="candidaterow">
    <td colspan=5>
      &nbsp; &nbsp; &nbsp; &nbsp;
      <a href="javascript:hpol.showMinorities('minoritati_2012')">
        <span id=min_link>+ minorități</span>
      </a>
    </td>
  </tr>
  {/if}
</table>

