{* Smarty *}

<div class="sidemoduletitle">
  Date de contact
</div>
<div style="padding-left:10px">
<span class="small">
  <table width=320 cellspacing=2 cellpadding=2 class="recent_news">
  {foreach from=$details key=dkey item=dval_array}
    <tr>
    <td valign="top">
      {$details_ro[$dkey]}
    </td>
    <td>
    {if count($dval_array) > 0}
      {foreach from=$dval_array item=dval}
      {strip}
        {if $dkey == "phone" or $dkey == "address"}
          {$dval}
        {else}
          {if $dkey == "email"}
            <a href="mailto:{$dval}">{$dval}</a>&nbsp;
          {else}
            <a href="{$dval}">{$dval}</a>&nbsp;
          {/if}
        {/if}
        <br />
      {/strip}
      {/foreach}
    {else}
      N/A
    {/if}
    </td>
  {/foreach}
  </table>
</span>
</div>
