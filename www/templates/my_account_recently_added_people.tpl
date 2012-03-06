{* Smarty *}

<b>Oameni adăugați recent pe harta politicii</b>
<br><br>
{section name=p loop=$recent_people}
{strip}

  {$recent_people[p].value},&nbsp;
  <span class="gray">
    {$recent_people[p].time|date_format:"%e&nbsp;%b"}
  </span>:&nbsp;

  <a href="/?cid=9&id={$recent_people[p].idperson}">
     {$recent_people[p].display_name}
  </a>

  <br>
{/strip}
{/section}
