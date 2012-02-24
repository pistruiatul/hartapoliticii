{* Smarty *}
<p>

<table width=100% cellspacing=2>
	 <tr>
	   <td colspan="2">Cea mai recenta activitate:</td>
	   <td>DA</td>
	   <td>NU</td>
	   <td>Ab</td>
	   <td>-</td>
	   <td align="right">vot</td>
	 </tr>
	 {section name=v loop=$votes}
	 {strip}
	   <tr>
	     <td valign="top">{counter name=v}.</td>
	     <td valign="top">
	       <div class="medium">
	       <a href="{$votes[v].link}">
	         {$votes[v].type}
	       </a>:&nbsp;
	       <a href="{$votes[v].law_link}">PL {$votes[v].law_number}</a>&nbsp;
	         {$votes[v].description}
	       &nbsp;
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
	   </tr>
	 {/strip}
 {/section}
</table>

<div style="text-align:right;font-size:12px">
  <a href="/?name={$name}&exp=senat/2008">Vezi toate voturile</a>
</div>
