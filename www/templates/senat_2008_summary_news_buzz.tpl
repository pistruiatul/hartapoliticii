{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=400 valign="top">
	    <div class="smalltitle"><strong>
			  Prezența în fluxul mediafax în ultimele 7 zile
	    </strong></div>
			<div class="medium gray">
			  (anotat cu procentul de prezență în parlament)
		  </div>
	    
	    {include file="news_buzz_top_people.tpl"}  
    </td>

    <td valign="top">
	    <div class="smalltitle"><strong>{$MOST_RECENT_NEWS_ABOUT} 
	      (via <a href="http://www.mediafax.ro">mediafax.ro</a>)</strong></div>
	    <div class="medium">

      {include file="news_buzz_latest_news.tpl"}  
      </div>  
    </td>
  </tr>
</table>
