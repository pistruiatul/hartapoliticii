{* Smarty *}

<table width=100% cellspacing="15">
  <td valign=top>

  Aceste anotări au fost create de către utilizatorul "<b>{$user_login}</b>".
  <span class="gray">
  Controlul asupra acestor anotări aparține în totalitate utilizatorului care
  le-a creat, iar Harta Politicii nu își nici un fel de
  responsabilitate asupra lor. Aceste anotări sunt nemoderate și autenticitatea
  lor este neverificată.
  Mai multe detalii găsiți <a href="http://www.hartapoliticii.ro/?p=4042">
  aici</a>.
  </span>

  {if $description != ""}
    <div class="tag_description">
      {$description}
    </div>
  {else}
    <br><br>
  {/if}

  <div style="clear:right"></div>
  <strong>Dacă următoarele presupuneri sunt adevărate:</strong>
  <br/>

  <div style="margin-left: 20px;">
  <table width="100%" cellpadding="0">
  <tr>
  <td><b>Descrierea votului</b></td>
  <td width="180" align="right"><b>Semnificața votului</b></td>
  </tr>
  <tr height="1"><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
  {section name=i loop=$votes}
    {strip}
    <tr>
      <td class="small">
        <a href="{$votes[i].link}">{$votes[i].type|trim}</a>:&nbsp;
        {$votes[i].description|trim}
        (<span class="gray">{$votes[i].time|date_format:"%d %b %Y"}</span>)
      </td>

      <td class="small" width="180" align="right" valign="top">
        {if $votes[i].inverse}
          <span class="red"><b>contra</b></span>
        {else}
          <span class="green"><b>pentru</b></span>
        {/if}
        &nbsp;
        {$tag}<br>
      </td>
    </tr>

    <tr height="1"><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
    {/strip}
  {/section}
  </table>
  </div>

  <br/>

  <strong>Atunci următorii parlamentari au votat în felul următor:</strong>
  <br/>
  <div style="margin-left: 20px;">
  {include file="compass_table_header.tpl"}

  {section name=p loop=$people}
    {strip}
    {include file="compass_person_row.tpl" person=$people[p] room=$room
        year=$year tagid=$tagid}
    {/strip}
  {/section}
  </div>
  <br/>  <br/>
  <strong>Parlamentari care nu au votat pe nici una din aceste legi:</strong>

  <div style="margin-left: 20px;">
  {include file="compass_table_header.tpl"}

  {section name=a loop=$absentees}
    {strip}
    {include file="compass_person_row.tpl" person=$absentees[a]}
    {/strip}
  {/section}
  </div>


  </td>
</table>
