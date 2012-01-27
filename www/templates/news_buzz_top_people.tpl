{* Smarty *}
 
 {section name=n loop=$newsPeople}
 {strip}
   {counter name=n}.&nbsp;
   <a href="?name={$newsPeople[n].name}">
     {$newsPeople[n].display_name}
   </a>
   {if ($newsPeople[n].percent!=NULL)}
     , <span class="gray">
     {$newsPeople[n].percent*100|string_format:"%.2f"}%</span>
   {/if}
   &nbsp;-&nbsp;
   {$newsPeople[n].mentions} mențiuni 
   &nbsp;
   <img valign="absmiddle" src="images/transparent.png"
    {if ($newsPeople[n].mentions_dif > 0)}
       class="up_arrow" 
       title="{$newsPeople[n].mentions_dif} articole în plus față de săptămâna anterioară"
    {else}
      class="down_arrow"
      title="{$newsPeople[n].mentions_dif*-1} articole în minus față de săptămâna anterioară"
    {/if}
    >
 
   <br>
 {/strip}
 {/section}