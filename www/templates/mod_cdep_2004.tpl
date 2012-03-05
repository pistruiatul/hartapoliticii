{* Smarty *}

<div class="parl_summary_panel">
  Prezența:
  <div class="parl_presence_number">
    {$presence|string_format:"%.2f"}%
  </div>
</div>
Deputat între <b>{$from}</b> și <b>{$to}</b>{$reason} din partea
<b>{$party}</b>
{if sizeof($party_list) > 0}
  (<span class="gray small">
  {section name=p loop=$parties}
    {strip}
      {$parties[p]},
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
Luări de cuvânt: {$speaking_time}.
<br>
<br>
Mai multe detalii pe
<a href="http://www.cdep.ro/pls/parlam/structura.mp?idm={$idm}&cam=2&leg=2004">
  site-ul cdep.ro</a>.
