{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=350 valign="top">
      <p class="smalltitle">
        <strong>Turul doi, 6 decembrie</strong>
      </p>   
      
      <table width=100%>
        <tr>
          <td valign="top">1.</td>
	        <td valign="top"><img src="images/people_tiny/3393.jpg"width="22" height="30"></td>
	        <td><a href="?cid=9&id=3393">Băsescu Traian</a></td>
	        <td valign="top">PD-L</td>
        </tr>
        <tr>
          <td valign="top">2.</td>
          <td valign="top"><img src="images/people_tiny/42_1.jpg"width="22" height="30"></td>
          <td><a href="?cid=9&id=42">Geoană Mircea Dan</a></td>
          <td valign="top">PSD+PC</td>
        </tr>
      </table>
    </td>
    
    <td valign="top" rowspan="2">
      <p class="smalltitle"><strong>{$MOST_RECENT_NEWS_ABOUT} 
        (via <a href="http://www.mediafax.ro">mediafax.ro</a>)
      </strong></p>
      <div class="medium">
      {include file="news_buzz_latest_news.tpl"}
      
      <p class="smalltitle"><strong>Prezența în fluxul mediafax în ultimele 10 zile
        (via <a href="http://www.mediafax.ro">mediafax.ro</a>)
      </strong></p>
        {* This section is almost identical to news_buzz_top_people. Should unite. *}
        {section name=n loop=$newsPeople}
        {strip}
          {counter name=n}.&nbsp;
          <a href="?cid=9&id={$newsPeople[n].idperson}">
            {$newsPeople[n].name}
          </a>
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
    </td>
  </tr>


  <tr>
    <td width=350 valign="top">
      <p class="smalltitle"><strong>Candidați în primul tur</strong></p>
      {include file="pres_2009_summary_candidates.tpl"}
    </td>
  </tr>
  
  <tr>
  <td colspan=2>
  {include file="video_section.tpl"}
  </td>
  </tr>
  
  
  <tr>
	  <td colspan=2>
	  <h4>Alte detalii</h4>
	  Alegerile prezidențiale vor avea loc pe <strong>22 Noiembrie 2009</strong>. 
	  <br>Pentru mai multe
	  informații despre legislație și secții de votare, vedeți site-ul 
	  <a href="http://www.roaep.ro/ro/section.php?id=atasamentescrutin&ids=50">
	    Autorității Electorale Centrale</a>.
	    
	  <br>Site-ul Biroului Electoral Central poate fi găsit 
    <a href="http://www.bec2009p.ro">aici</a>.
	  </td>
  </tr>
</table>


