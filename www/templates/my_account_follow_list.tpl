{section name=x loop=$followed_people}
  <div style="margin-left: 20px;">
    <a href="?name={$followed_people[x]->displayName}">
      {$followed_people[x]->displayName}
    </a>
  </div>
{sectionelse}
  Poți urmări politicieni pentru a alfa doar știrile care îi privesc pe aceștia.
  <br>
  Mai <a href="">multe detalii aici</a>.
{/section}

Vezi <a href="/?cid=14&sid=1">toate știrile cu acești politicieni</a>.

