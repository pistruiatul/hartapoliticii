{* Smarty *}

<div class="sidemoduletitle">
	  Declarații
	</div>
	<div style="padding-left:10px">
		<table width=320 cellspacing=2 cellpadding=2 class="recent_news">
		  {section name=n loop=$declarations}
		  {strip}
		    <tr>
		    <td valign="top">
		     <span class="small">
           <em>{$declarations[n].time|date_format:"%d&nbsp;%b"}</em><br>
           <em>
             <span class="light_gray">
               {$declarations[n].time|date_format:"%Y"}
             </span>
           </em><br>
         </span>
		    </td><td valign="top">

         <div class="small recent_news_title">
           {$declarations[n].declaration|truncate:200:"...":true}&nbsp;
           <div class="declaration_source">
             <img src="images/popout_icon.gif" border="0"
                  width="12" height="12">&nbsp;
             <span class="gray">
               sursa: <a href="{$declarations[n].source}">stenograme parlament</a>
             </span>
           </div>
           </a><br>
		     </div>
		    </td>
		    </tr>
		  {/strip}
		  {/section}
		</table>
	</div>
	<div class="module_expand_link">
	  <a href="?name={$name}&exp=person_declarations">
      mai multe declarații...</a>
	</div>
