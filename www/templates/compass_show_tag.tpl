{* Smarty *}

<table width=970 cellspacing="15">
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


  <br><br>
  <div style="clear:right"></div>
  <strong>Dacă următoarele presupuneri sunt adevărate:</strong>
  <br/>

  <div style="margin-left: 20px;">
  <table width="930" cellpadding="0">
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
  {include file="parl_person_beliefs_header.tpl" width=300}

  {section name=p loop=$people}
    {strip}
    {include file="parl_person_belief.tpl"
        w1=$people[p].w1
        w2=$people[p].w2
        w3=$people[p].w3
        w4=$people[p].w4
        w5=$people[p].w5
        c2=$people[p].c2
        c3=$people[p].c3
        c4=$people[p].c4
        tiny_photo=$people[p].tiny_photo
        display_name=$people[p].display_name
        taglink=$people[p].link
        party_name=$people[p].party_name
        width=300}
    {/strip}
  {/section}
  </div>
  <br/>  <br/>
  <strong>Parlamentari care nu au votat pe nici una din aceste legi:</strong>

  <div style="margin-left: 20px;">
  {include file="parl_person_beliefs_header.tpl" width=300}

  {section name=a loop=$absentees}
    {strip}
    {include file="parl_person_belief.tpl"
        w1=$absentees[a].w1
        w2=$absentees[a].w2
        w3=$absentees[a].w3
        w4=$absentees[a].w4
        w5=$absentees[a].w5
        c2=$absentees[a].c2
        c3=$absentees[a].c3
        c4=$absentees[a].c4
        tiny_photo=$absentees[a].tiny_photo
        display_name=$absentees[a].display_name
        taglink=$absentees[a].link
        party_name=$absentees[a].party_name
        width=300}
    {/strip}
  {/section}
  </div>


  </td>
</table>
