{* Smarty *}

<table>
  <td width="{if $width}{$width}{else}140{/if}">
    <a href="{$taglink}">{$tag|escape:html}</a>
  </td>
  <td align="right">
    <table width="440" cellspacing="0" cellpadding="0" class="belief_table">
      <td width="{$w1}" bgcolor="#ececec">
      &nbsp;</td>
      <td width="{$w2}" bgcolor="#c7470f" align="center">
      {$c2}</td>
      <td width="{$w3}" bgcolor="#8e8e8e" align="center">
      {$c3}</td>
      <td width="{$w4}" bgcolor="#82b83d" align="center">
      {$c4}</td>
      <td width="{$w5}" bgcolor="#ececec">
      &nbsp;</td>
    </table>
  </td>
</table>
