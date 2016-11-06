{* Smarty *}

{include file="electoral_college_search.tpl"}

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign="top">

    Lista exhaustivă a colegiilor electorale:<br><br>
<table cellpadding=5 cellspacing=0>
{foreach from=$colleges item=college key=county name=counties}
  {if $smarty.foreach.counties.index % 2 == 0}
    <tr>
  {/if}
    <td width="100" valign="top">
      <b>{$county|ucwords}</b>:
    </td>
    <td width="350" valign="top">
      {section loop=$college name=c}
        <a href="?cid=23&colegiul={$college[c]}+{$county|lower|replace:' ':'+'}">{$college[c]|ucwords}</a>&nbsp;&nbsp;
      {/section}
    </td>

{/foreach}
<table>

  </td>
</tr>
</table>