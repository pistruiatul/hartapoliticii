{* Smarty *}


{section name=i loop=$tags}
  {strip}

  <a href="?cid=15&tagid={$tags[i].id}&room={$room}">
    {$tags[i].tag}
  </a>&nbsp;
  <span class="gray">({$tags[i].num})</span>,

  {/strip}
{/section}
