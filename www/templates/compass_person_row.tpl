{* Smarty *}

<table width="100%">
  <td width="333" height=25>
    <img src="{$person.tiny_photo}" valign="middle" vspace=0 height=20>
    <a href="{$person.link}">{$person.display_name|escape:html}</a>
     <span class="small light_gray">{$person.party_name}</span>
  </td>
  <td align="right" width="400">
    <table width="400" cellspacing="0" cellpadding="0" class="belief_table">
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

      <td width="{$gray_px_rights}" bgcolor="#ececec">
        &nbsp;
      </td>
    </table>
  </td>
  <td width="30" align="center">
    <div class="belief_total">
      = {$person.yes_cnt-$person.no_cnt}
    </div>
  </td>

  <td width="20">
    <a href="javascript:compassShowDetailsFor({$person.id}, '{$room}', {$year},
             {$tagid});">
      <img src="/images/plus.png" border=0
           id="compass_details_link_{$tagid}_{$person.id}">
    </a>
  </td>
  <td align="right" width="120">
    <div style="width:120px">
    {if $person.missing_cnt > 0}
      <span class="light_gray">
      {$person.missing_cnt} abs
      </span>
    {/if}

    {if $person.abs_cnt > 0}
      <span class="gray">
      , {$person.abs_cnt} abțineri
      </span>
    {/if}
    </div>
  </td>
</table>

<div id="compass_vote_details_{$tagid}_{$person.id}" style="display:none">
</div>
