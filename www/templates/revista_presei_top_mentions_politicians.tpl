{* Smarty *}

<table width=100%>
{section name=c loop=$topPeople}
{strip}
  <tr>
    <td valign="top" width=20>{$smarty.section.c.index+1}.</td>
    <td valign="top" width=25>
      <img src="{$topPeople[c].tiny_photo}" width="22" height="30">
    </td><td width=200>
    <a href="?name={$topPeople[c].name}">
       {$topPeople[c].display_name}
      </a>
    </td>
    <td valign="top" width=70>
      <img valign="absmiddle" src="images/transparent.png"
      {if ($topPeople[c].mentions_dif > 0)}
         class="up_arrow" 
         title="{$topPeople[c].mentions_dif} articole în plus față de săptămâna anterioară"
      {else}
        class="down_arrow"
        title="{$topPeople[c].mentions_dif*-1} articole în minus față de săptămâna anterioară"
      {/if}
      >&nbsp;{$topPeople[c].mentions}
    </td>
    
    {if $SHOW_LATEST_ARTICLE}
    <td valign="top">
      <span class="small gray"><em>
        {$topPeople[c].article_time|date_format:"%d&nbsp;%b"}
      </em></span> 
      &nbsp; <a href="{$topPeople[c].article_link}" class="black_link">
       <span class="medium">{$topPeople[c].article_title}</span>&nbsp;
       <img src="images/popout_icon.gif" border="0" width="12" height="12">
      </a>
    </td>
    {/if}
  </tr>
{/strip}
{/section}
<tr>
<td colspan=4 align="right">
<span class="medium">
  ... dintr-un total de {$numArticles} articole.<br>
</span>

</td></tr>
<tr>
<td colspan=4>
<span class="medium">
  Pentru fiecare persoană sunt afișate numărul de articole distincte în care
  apare numele său în ultimele 7 zile.
</span>
</td>
</tr>
</table>