{* Smarty *}

{foreach from=$governments item=government name=government}
  {strip}
  <table width="590">
    {assign var="i" value=$smarty.foreach.government.index}

    {include file="mod_gov_ro_expanded_person_row.tpl"
        person=$prime_ministers[$i]
        highlight=true}

    {include file="mod_gov_ro_expanded_person_row.tpl"
        person=$viceprime_ministers[$i]}

    {if not $positions[$i].title|stristr:'prim'}
      {include file="mod_gov_ro_expanded_person_row.tpl" person=$positions[$i]}
    {/if}

    {foreach from=$government item=member name=member}
      {include file="mod_gov_ro_expanded_person_row.tpl" person=$member}
    {/foreach}

  </table>
  <hr>
  {/strip}
<br />
{/foreach}
