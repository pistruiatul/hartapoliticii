{* Smarty *}


{section name=i loop=$tags}
  {strip}

  <a href="?cid=15&tagid={$tags[i].id}&room={$room}&u={$tags[i].uid}&csum={$tags[i].csum}">
    {$tags[i].tag}
  </a>&nbsp;
  <span class="gray">({$tags[i].num})</span>,

  {/strip}
{/section}
