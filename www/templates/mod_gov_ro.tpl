{* Smarty *}

<table>
{section name=p loop=$positions}
  {strip}
  <tr>
    <td valign="top">
      <div class="position_dates">
        <nobr>
        {$positions[p].time_from|date_format:"%e %b %Y"}
        </nobr>
      </div>
    </td>
    <td valign="top"><div class="position_dates">&nbsp;-&nbsp;</div></td>
    <td valign="top">
      <div class="position_dates">
        {if $positions[p].time_to == 0}
          prezent
        {else}
          <nobr>
          {$positions[p].time_to|date_format:"%e %b %Y"}
          </nobr>
        {/if}
      </div>:
    </td>

    <td valign="top">
      <a href="?name={$person_name}&exp=gov/ro">
        {$positions[p].title}</a> în cabinetul&nbsp;
      <a href="?cid=9&id={$positions[p].cabinet.person_id}&exp=gov/ro">
        {$positions[p].cabinet.display_name}</a><br>
    </td>
  </tr>
  {/strip}
{/section}
</table>
