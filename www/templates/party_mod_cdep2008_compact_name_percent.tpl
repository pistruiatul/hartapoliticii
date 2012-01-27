{* Smarty *}
<tr>
  <td valign="top" width=25>
    <img src="{$person.tiny_photo}" width="22" height="30">
  </td>
  <td valign="top" class="medium">
    <a href="?cid=9&id={$person.idperson}">
      {$person.name}
    </a>&nbsp;
    <span class="medium gray">
      {$percent|string_format:"%.2f"}%&nbsp; 
    </span>
  </td>
</tr>