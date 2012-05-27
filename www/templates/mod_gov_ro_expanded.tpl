{* Smarty *}

{foreach from=$governments item=government name=government}
  {strip}
<table>
  {assign var="i" value=$smarty.foreach.government.index}
  {assign var="prime_minister" value=$prime_ministers[$i]}
  <tr>
    <td>
      {$prime_minister.title}
    </td>
    <td>
      {$prime_minister.display_name}
    </td>
    <td>
      <nobr>
      {$prime_minister.time_from|date_format:"%e %b %Y"} - 
      </nobr>
      {if $prime_minister.time_to == 0}
        prezent
      {else}
        <nobr>
        {$prime_minister.time_to|date_format:"%e %b %Y"}
        </nobr>
      {/if}
    </td>
  </tr>
  {assign var="viceprime_minister" value=$viceprime_ministers[$i]}
  <tr>
    <td>
      {$viceprime_minister.title}
    </td>
    <td>
      {$viceprime_minister.display_name}
    </td>
    <td>
      <nobr>
      {$viceprime_minister.time_from|date_format:"%e %b %Y"} - 
      </nobr>
      {if $viceprime_minister.time_to == 0}
        prezent
      {else}
        <nobr>
        {$viceprime_minister.time_to|date_format:"%e %b %Y"}
        </nobr>
      {/if}
    </td>
  </tr>
  {assign var="position" value=$positions[$i]}
  {if not $position.title|stristr:'prim'}
  <tr>
    <td>
      {$position.title}
    </td>
    <td>
      {$position.display_name}
    </td>
    <td>
      <nobr>
      {$position.time_from|date_format:"%e %b %Y"} - 
      </nobr>
      {if $position.time_to == 0}
        prezent
      {else}
        <nobr>
        {$position.time_to|date_format:"%e %b %Y"}
        </nobr>
      {/if}
    </td>
  {/if}
  </tr>
{foreach from=$government item=member name=member}
  <tr>
    <td>
    {$member.title}
    </td>
    <td>
    {$member.display_name}
    </td>
    <td>
      <nobr>
      {$member.time_from|date_format:"%e %b %Y"} - 
      </nobr>
      {if $member.time_to == 0}
        prezent
      {else}
        <nobr>
        {$member.time_to|date_format:"%e %b %Y"}
        </nobr>
      {/if}
    </td>
  </tr>
{/foreach}
  {/strip}
</table>
<br />
{/foreach}
