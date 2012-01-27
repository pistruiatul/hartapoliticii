{* Smarty *}

<table width=970 cellspacing=15>
<td>
	Harta Politicii învață semi-automat din presă fapte despre politicieni. 
  Această pagină listează cele mai recente fapte învățate, cu data la care
  fapta a fost descoperită și link către sursă. Este o pagină creată în scop 
  demonstrativ. :-)<br/><br/>
	<table width=100%>
	{section name=f loop=$facts}
	{strip}
	  <tr>
	    <td valign="top">{counter name=f}.</td>
	    <td valign="top">
	      <img src="{$facts[f].tiny_photo}" width="22" height="30">
	    </td>
	    <td>
	      <a href="?cid=9&id={$facts[f].idperson}">
	        {$facts[f].display_name}
	      </a>,&nbsp; 
	      {$facts[f].qualifier},&nbsp;
	      <span class="gray">
	        {$facts[f].time|date_format:"%d %b %Y"},&nbsp;
	        {$facts[f].source}&nbsp;
	        <a href="{$facts[f].link}" target="_blank">
	          <img src="images/popout_icon.gif" border="0" width="12" height="12">
	        </a>
	      </span>
	    </td>
	  </tr>
	{/strip}
	{/section}
	</table>
</td>
</table>