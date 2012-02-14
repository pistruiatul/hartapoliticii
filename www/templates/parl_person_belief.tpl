{* Smarty *}

<table>
  <td width="{if $width}{$width}{else}140{/if}" height=25>
    <img src="{$tiny_photo}" valign="middle" vspace=0 height=20>
    <a href="{$taglink}">{$display_name|escape:html}</a>
    <span class="small light_gray">{$party_name}</span>
  </td>
  <td align="right">
    <table width="440" cellspacing="0" cellpadding="0" class="belief_table">
      {* Gray section in the beginning *}
      <td width="{$w1}" bgcolor="#ececec">
        &nbsp;
      </td>

      {* The red section of votes against *}
      <td width="{$w2}" bgcolor="#c7470f" align="center">
        {if $c2 > 0}
          {$c2}
        {/if}
      </td>

      <td width=2 bgcolor="#333" align="center">
      </td>

      {* Pro votes *}
      <td width="{$w4}" bgcolor="#82b83d" align="center">
        {if $c4 > 0}
          {$c4}
        {/if}
      </td>

      <td width="{$w5}" bgcolor="#ececec">
        &nbsp;
      </td>
    </table>
  </td>
  <td align="right" width="150">

      {if $c3 > 0}
        <span class="light_gray">
        {$c3} absențe
        </span>
      {/if}

      {if $c5 > 0}
        <span class="gray">
        , {$c5} abțineri
        </span>
      {/if}
    </span>
  </td>
</table>
