<div class="mentions_block">
  {section name=x loop=$people}
  {strip}
    <div class="news_list_mention {if $people[x].following}following{/if}{if $people[x].highlight} highlight{/if}"
         id="mention_{$news_id}_{$smarty.section.x.index}"
         {if $smarty.section.x.index>7}
         style="display:none;"
         {/if}>
      <a href="?name={$people[x].name}">
        {$people[x].display_name}
      </a>
    </div>

  {if $smarty.section.x.index==7}
    <nobr>
    ... <a class="gray" href="javascript:hpol.showAllNewsMentions('{$news_id}', {$people|@count});">plus alți {$above_seven}</a>
    </nobr>
  {/if}

  {/strip}
  {/section}
</div>