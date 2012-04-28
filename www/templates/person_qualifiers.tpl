{* Smarty *}

{if count($qualifiers) > 0}
  <div class="sidemoduletitle">
    Tag-uri din presă
  </div>
  <div style="padding-left:10px; margin-bottom:5px" class="medium">
      {section name=q loop=$qualifiers}
      {strip}
         {$qualifiers[q].qualifier}&nbsp;
         <a href="{$qualifiers[q].link}" class="black_link">
           <img src="images/popout_icon.gif" border="0" width="12" height="12">
         </a>
      {/strip}
      {/section}
  </div>
{/if}
