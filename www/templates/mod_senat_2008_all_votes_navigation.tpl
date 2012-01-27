{if $prevStart >= 0}
  <a href="/?name={$name}&exp={$room}/2008&start={$prevStart}&maverick={$maverick}">Prev</a>
{else}
  Prev
{/if}
&nbsp;/&nbsp; 
{if $nextStart >= 0}
  <a href="/?name={$name}&exp={$room}/2008&start={$nextStart}&maverick={$maverick}">Next</a>
{else}
  Next
{/if}

/ Arată doar voturile 
<a href="/?name={$name}&exp={$room}/2008&maverick=1">
  diferite de linia de partid.
</a>
