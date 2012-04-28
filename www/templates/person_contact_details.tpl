{* Smarty *}

<div class="sidemoduletitle">
  Date de contact
</div>

<div style="margin: 10px 0 5px 10px;" class="medium">
  <table cellspacing="0" cellpadding="0" width="100%">
    {if count($facebook) > 0}
      <td>
      <div style="margin-right: 20px" class="contact_block">
        <img src="images/person_facebook.png" align="absmiddle">
        <a href="{$facebook[0]}" target="_blank">
          Cont pe facebook
        </a>
      </div>
      </td>
    {/if}

    {if count($twitter) > 0}
    <td>
    <div class="contact_block">
      <img src="images/person_twitter.png" align="absmiddle">
      <a href="{$twitter[0]}" target="_blank">
        Cont pe twitter
      </a>
    </div>
    </td>
    {/if}
  </table>

  {if count($website) > 0}
    <div class="contact_block" style="white-space: nowrap;">
      {foreach from=$website item=w name="website"}
        <img src="images/person_web.png" align="absmiddle">
        <a href="http://{$w}" target="_blank">{$w}</a>
        {if !$smarty.foreach.website.last}
          <br>
        {/if}
      {/foreach}
    </div>
  {/if}

  {if count($email) > 0}
    <div class="contact_block">
      {foreach from=$email item=e name="email"}
        <span style="white-space: nowrap;">
        <img src="images/person_email.png" align="absmiddle">
        <a href="mailto:{$e}">{$e}</a>
        {if !$smarty.foreach.email.last}
          <br>
        {/if}
        </span>
      {/foreach}
    </div>
  {/if}


  {if count($address) > 0}
    <div class="contact_block">
      {foreach from=$address item=a}
        <div class="address">
          <img src="images/person_address.png" align="absmiddle">
          {$a}
        </div>
      {/foreach}
    </div>
  {/if}

  {if count($phone) > 0}
    <div class="contact_block">
      {foreach from=$phone item=p name="phone"}
        <img src="images/person_phone.png" align="absmiddle">
        {$p}
        {if !$smarty.foreach.phone.last}
          /
        {/if}
      {/foreach}
    </div>
  {/if}
</div>

<div class="module_expand_link">
  <a href="http://agenda.grep.ro/" target="_blank">modifică...</a>
</div>
