{* Smarty *}

<table align="center">
  <td align="center" width="130">
    <div id="support_person"
      {if $supported_by_logged_in_user}style="display:none"{/if}>
      <a href="javascript:hpol.supportOnFacebook('{$person_url}', true)">
        <b>Susține!</b><br><span class="small">public pe facebook</span></a>
    </div>

    <div id="un_support_person"
      {if !$supported_by_logged_in_user}style="display:none"{/if}>
      <script type="text/javascript">
        hpol.supportActionId = '{$supported_by_logged_in_user}';
      </script>
      <a href="javascript:hpol.supportOnFacebook('{$person_url}', false)">
      <span class="red"><b>Susții!</b></span><br>
      <span class="small red">retrage susținerea</span></a>
    </div>
  </td>

  <td>
    <div class="follow_button {if $following}unfollow{else}follow{/if}"
         person_id="{$person_id}"
         action="{if $following}unfollow{else}follow{/if}">
    </div>
  </td>
</table>

<div id="under_image_log" style="text-align: center"></div>

{if $num_supporters > 0}
  <div class="supporters">
    Număr susținători: <span class="number">{$num_supporters}</span>
  </div>
{/if}
