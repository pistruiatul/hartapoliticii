<table width=100% cellpadding=2>
  <tr bgcolor="#EEEEEE">
    <td>Candidat</td>
    <td>Partid</td>
    <td>Voturi</td>
  </tr>

  {section name=c loop=$candidates}

  {if $candidates[c].id == $id_winner}
    {assign var="row_css_class" value="winnerrow"}
  {else}
    {assign var="row_css_class" value="othersrow"}
  {/if}

  <tr class="{$row_css_class}{if $candidates[c].minoritati == 1} minoritati{/if}"
      name="{if $candidates[c].minoritati == 1}minoritati{/if}">
    <td>
      <a href="?name={$candidates[c].name|replace:' ':'+'}">
      {$candidates[c].display_name}
      </a>

    </td>
    <td>{$candidates[c].party}</td>
    <td>
      {$candidates[c].voturi},
      {if $candidates[c].id == $id_winner}
        câștigător
      {else}
        <span class="small gray">
          cu {$candidates[c].difference} {$candidates[c].reason}
        </span>
      {/if}
    </td>
  </tr>
  {/section}

</table>

{* only show this if there actually are minorities candidates around *}
{if $show_minorities_link}
  <a href="javascript:hpol.showMinorities()">
    <span id=min_link>+ minorități</span>
  </a>
{/if}
