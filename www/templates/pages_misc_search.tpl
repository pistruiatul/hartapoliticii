{* Smarty *}


<table width=970 cellspacing=15>
  <td>
    {* ---------------------------------------------- *}
    {* People search *}

    <p>
      Persoane care se potrivesc cu "<b>{$query}</b>"
    </p>

    {section name=p loop=$persons}
      <div class=searchresult>
        <img class=thumb src={$persons[p].tiny_img_url} align=absmiddle>
        <div class=name>
          <a href="?cid=9&id={$persons[p].id}&ssid={$ssid}&ssp={$smarty.section.p.index}">
            {$persons[p].display_name}</a>

        </div>

        <div class=snippet>
          {$persons[p].history_snippet}
        </div>
      </div>
    {sectionelse}
      Nu am găsit persoane cu numele ăsta.
    {/section}

    {* ---------------------------------------------- *}
    {* Declaration search *}
    <br>
    <p>
      Declarații care se potrivesc cu "<b>{$query}</b>"
    </p>

    {section name=d loop=$declarations}
      <div class=searchresult>
        <div class=name>
          {$declarations[d].display_name}
          :
          <a href="/?name={$declarations[d].name}&exp=person_declarations&dq={$query}">
            {$declarations[d].cnt} mențiuni</a>
        </div>
      </div>
    {sectionelse}
      Nu am găsit persoane cu numele ăsta.
    {/section}

  </td>
</table>
