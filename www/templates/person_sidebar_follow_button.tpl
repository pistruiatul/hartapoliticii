{* Smarty *}

<table align="center">
  {if $person_id==5230}
  <td align="center" width="130">
    <a href="javascript:hpol.supportOnFacebook('{$person_url}')">
      <b>Susține!</b><br>
      <span class="small">public pe facebook</span></a>
  </td>
  {/if}

  <td>
    <div class="follow_button {if $following}unfollow{else}follow{/if}"
         person_id="{$person_id}"
         action="{if $following}unfollow{else}follow{/if}">
    </div>
  </td>
</table>
