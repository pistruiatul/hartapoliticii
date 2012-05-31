{* Smarty *}

<tr class="gov_person_row">
  <td valign="top">
    <img src="{$person.tiny_img_url}" class="gov_avatar">
  </td>
  <td valign="top" width="60%">
    <div class="gov_name">
      <a href="/?cid=9&id={$person.idperson}">
        {$person.display_name}
      </a>
    </div>
    <div class="gov_snippet">
      {$person.history_snippet}
    </div>
  </td>

  <td valign="top">
    <div class="gov_position{if $highlight}_highlighted{/if}">
      {$person.title}<br>
      <span class="time_period">
      <nobr>
        {$person.time_from|date_format:"%e %b %Y"} -
        {if $person.time_to == 0}
          prezent
        {else}
          {$person.time_to|date_format:"%e %b %Y"}
        {/if}
      </nobr>
      </span>
    </div>
  </td>

</tr>