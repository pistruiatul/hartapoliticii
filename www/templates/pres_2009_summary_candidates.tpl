{* Smarty *}

<table width=100%>
{section name=c loop=$candidati}
{strip}
  <tr>
    <td valign="top">{counter name=c}.</td>
    <td valign="top">
      <img src="{$candidati[c].tiny_photo}"
        width="22" height="30">
    </td><td>
    <a href="?cid=9&id={$candidati[c].idperson}">
       {$candidati[c].name}
      </a>
    </td>
    <td valign="top">
      {$candidati[c].party_name}
    </td>
  </tr>
{/strip}
{/section}
</table>
