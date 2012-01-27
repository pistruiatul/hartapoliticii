{* Smarty *}
<p>

{include file="mod_senat_2008_all_votes_navigation.tpl" room="cdep"}


<table width=100% cellspacing=2>
 <tr>
   <td colspan="2">Chestiune votată:</td>
   <td>DA</td>
   <td>NU</td>
   <td>Ab</td>
   <td>-</td>
   <td>Vot</td>
   <td></td>
 </tr>
 {section name=v loop=$votes}
 {strip}
   <tr>
     <td valign="top">{$start++}.</td>
     <td valign="top">
       <div class="medium">
         <a href="{$votes[v].link}">
           {$votes[v].type}
         </a>:&nbsp;
         {$votes[v].description}&nbsp;
         <span class="small gray">
           {$votes[v].time|date_format:"%d %b %Y, %H:%M"}
         </span>
       </div>
     </td>

     <td valign="top" class="small gray">{$votes[v].vda}</td>
     <td valign="top" class="small gray">{$votes[v].vnu}</td>
     <td valign="top" class="small gray">{$votes[v].vab}</td>
     <td valign="top" class="small gray">{$votes[v].vmi}</td>
     <td valign="top" width="40" align="right">{$votes[v].vote}</td>
     <td valign="top" align="right">
     {if $votes[v].maverick == 1}
       <span class="red"><b>*</b></span>
     {/if}</td>
   </tr>
 {/strip}
 {/section}

</table>

{include file="mod_senat_2008_all_votes_navigation.tpl" room="cdep"}
