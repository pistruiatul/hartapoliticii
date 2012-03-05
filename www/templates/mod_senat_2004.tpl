{* Smarty *}

<div class="parl_summary_panel">
  Prezența:
  <div class="parl_presence_number">
    {$presence|string_format:"%.2f"}%
  </div>
</div>
Senator între <b>{$from}</b> și <b>{$to}</b>{$reason} din partea
<b>{$party}</b>
{if sizeof($party_list) > 0}
  (<span class="gray small">
  {section name=p loop=$party_list}
    {strip}
      {$party_list[p]},
    {/strip}
  {/section}
  </span>)
{/if}.
<br>
Prezent la
<b><span class={$number_class}>{$presence|string_format:"%.2f"}%</span></b>
din voturile
electronice dintre {$votes_from} și {$votes_to} ({$possible}).
<br>
<br>
Mai multe detalii pe
<a href="http://www.cdep.ro/pls/parlam/structura.mp?idm={$idm}&cam=1&leg=2004">
  site-ul cdep.ro</a>.
