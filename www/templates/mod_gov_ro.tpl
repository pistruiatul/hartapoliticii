{* Smarty *}

{section name=p loop=$positions}
  {strip}
    <div class="position_dates">
      {$positions[p].time_from|date_format:"%e %b %Y"} -&nbsp;
      {if $positions[p].time_to == 0}
        prezent
      {else}
        {$positions[p].time_to|date_format:"%e %b %Y"}
      {/if}

    </div>:&nbsp;

    <a href="{$positions[p].link}">
      {$positions[p].title}</a> în cabinetul&nbsp;
    <a href="?cid=9&id={$positions[p].cabinet.person_id}">
      {$positions[p].cabinet.display_name}</a><br>
  {/strip}
{/section}
