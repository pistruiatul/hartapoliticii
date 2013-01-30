{* Smarty *}

<div class="sidemoduletitle">
	  <a href="{$person->getRssDeclarationUrl()}">
		<img src="wp-includes/images/rss.png" alt="Flux RSS"></a> &nbsp;
	  Declarații
	</div>
	<div style="padding-left:0">
		<table width=290 cellspacing=2 cellpadding=2 class="recent_news">
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
           <br>
		     </div>
		    </td>
		    </tr>
		  {/strip}
		  {/section}
		</table>
	</div>
	<div class="module_expand_link">
	  <a href="{$person->getPersonDeclarationsUrl()}">
      mai multe declarații...</a>
	</div>
	