{* Smarty *}


<table width=970 cellspacing=15>
  <td valign="top">
    {* ---------------------------------------------- *}
    {* People search *}
    {if sizeof($persons) > 0}
      <p>
        Persoane care se potrivesc cu "<b>{$query}</b>"
      </p>
    {/if}

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
      <div class=searchresult>
      Nu am găsit persoane cu numele ăsta.
      </div>
    {/section}

    {* ---------------------------------------------- *}
    {* Declaration search *}
    <br>
    {if sizeof($declarations) > 0}
      <p>
        Declarații care se potrivesc cu "<b>{$query}</b>"
      </p>
    {/if}

    {section name=d loop=$declarations}
      <div class=searchresult>
        <div class=name>
          {$declarations[d].display_name}
          :
          <a href="/?name={$declarations[d].name}&exp=person_declarations&dq={$query}&ssid={$ssid}&ssp={$smarty.section.p.index+100}">
            {$declarations[d].cnt} mențiuni</a>
        </div>
      </div>
    {sectionelse}
      {if $searched_declarations}
        <div class=searchresult>
          Nu am găsit termenul ăsta în declarații.
        </div>
      {else}
        <div class=searchresult>
          <b>Pentru a căuta în declarații,
          <a href="?cid=search&q={$query}&d=true">click aici</a>.
          </b>
        </div>

      {/if}
    {/section}

  </td>
  <td width=50% valign="top">
    {* ---------------------------------------------- *}
    {* People search *}
    {if sizeof($colleges) > 0}
      <p>
        Colegii electorale care se potrivesc cu "<b>{$query}</b>"
      </p>
    {/if}

    {foreach from=$colleges item=result}
      <div class=searchresult>
        <div class=name>
          <a href="/?cid=23&colegiul={$result.name|replace:' ':'+'}">
            {$result.name|ucwords}</a>
        </div>
        <div style="margin-left:10px">
          {section name=d loop=$result.description}
            {$result.description[d]}<br>
          {/section}
          ...
        </div>
      </div>
    {/foreach}

  </td>
</table>
