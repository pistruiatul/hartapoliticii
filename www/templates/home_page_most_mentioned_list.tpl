<table width=100% cellspacing=4 cellpadding=0>
{section name=c loop=$people}
{strip}
  <tr>
    <td valign="top" width=25>
      <img src="{$people[c].tiny_photo}" width="22" height="30">
    </td>
    <td width=205 class="medium">
      <a href="?name={$people[c].name}">
        {$people[c].display_name}
      </a>
      &nbsp;
      <img valign="absmiddle" src="images/transparent.png"
      {if ($people[c].mentions_dif > 0)}
         class="up_arrow"
         title="{$people[c].mentions_dif}
                articole în plus față de săptămâna anterioară"
      {else}
        class="down_arrow"
        title="{$people[c].mentions_dif*-1}
               articole în minus față de săptămâna anterioară"
      {/if}
      >&nbsp;{$people[c].mentions}
    </td>
  </tr>
{/strip}
{/section}
</table>