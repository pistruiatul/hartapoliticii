{* Smarty *}

{if count($news) > 0}
	<div class="sidemoduletitle">
	  Știri recente
	</div>
	<div style="padding-left:10px">
		<table width=320 cellspacing=2 cellpadding=2 class="recent_news">
		  {section name=n loop=$news}
		  {strip}
		    <tr>
		    <td valign="top">
		     <span class="small">
           <em>{$news[n].time|date_format:"%d&nbsp;%b"}</em><br>
           <em><span class="light_gray">{$news[n].time|date_format:"%Y"}</span></em><br>
         </span>
		    </td><td>

         <div class="small recent_news_title">
         <span class="black_link">
  		     <a href="{$news[n].link}">
             {$news[n].title}&nbsp;
             <nobr>
               <img src="images/popout_icon.gif" border="0"
                    width="12" height="12" hspace="5">
               <span class="gray">{$news[n].source}</span>
             </nobr>
           </a></span><br>
		     </div>
		    </td>
		    </tr>
		  {/strip}
		  {/section}
		</table>
	</div>
	<div class="module_expand_link">
	  <a href="?name={$name}&exp=news">mai multe știri...</a>
	</div>

{/if}
