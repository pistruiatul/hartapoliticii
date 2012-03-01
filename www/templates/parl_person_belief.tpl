{* Smarty *}

<table>

  <tr>
  <td width="310" height=25>
    <a href="{$taglink}">{$tag|escape:html}</a>

  </td>
  <td align="right">
    <table width="240" cellspacing="0" cellpadding="0" class="belief_table">
      {* Gray section in the beginning *}
      <td width="{$person.gray_px_left}" bgcolor="#ececec">
        &nbsp;
      </td>

      {* The red section of votes against *}
      <td width="{$person.red_px}" bgcolor="#c7470f" align="center">
        {if $person.no_cnt > 0}
          -{$person.no_cnt}
        {/if}
      </td>

      <td width=1 bgcolor="#333" align="center">
      </td>

      {* Pro votes *}
      <td width="{$person.green_px}" bgcolor="#82b83d" align="center">
        {if $person.yes_cnt > 0}
          {$person.yes_cnt}
        {/if}
      </td>

      <td width="{$person.gray_px_right}" bgcolor="#ececec">
        &nbsp;
      </td>
    </table>
  </td>
  <td width="20">
    <a href="javascript:compassShowDetailsFor({$person_id}, '{$room}', {$year},
             {$tagid});">
      <img src="/images/plus.png" border=0
           id="compass_details_link_{$tagid}_{$person_id}">
    </a>
  </td>
  </tr>

  <tr>
    <td colspan="3">
      <div id="compass_vote_details_{$tagid}_{$person_id}" style="display:none">
      </div>

      <div class="score_card_description">
      {$description}
      </div>
    </td>
  </tr>
</table>

