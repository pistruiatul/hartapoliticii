{* Smarty *}

<table width="100%">
  <td width="50%" height=25>
    <img src="{$person.tiny_photo}" valign="middle" vspace=0 height=20>
    <a href="{$person.link}">{$person.display_name|escape:html}</a>
     <span class="small light_gray">{$person.party_name}</span>
  </td>
  <td align="right" width="400">
    <table width="400" cellspacing="0" cellpadding="0" class="belief_table">
      {* Gray section in the beginning *}
      <td width="{$w1}" bgcolor="#ececec">
        &nbsp;
      </td>

      {* The red section of votes against *}
      <td width="{$w2}" bgcolor="#c7470f" align="center">
        {if $c2 > 0}
          {$c2}
        {/if}
      </td>

      <td width=2 bgcolor="#333" align="center">
      </td>

      {* Pro votes *}
      <td width="{$w4}" bgcolor="#82b83d" align="center">
        {if $c4 > 0}
          {$c4}
        {/if}
      </td>

      <td width="{$w5}" bgcolor="#ececec">
        &nbsp;
      </td>
    </table>
  </td>
  <td width="20">
    <a href="javascript:compassShowDetailsFor({$person.id}, '{$room}', {$year},
             {$tagid});">
      <img src="/images/plus.png" border=0 id="compass_details_link_{$tagid}_{$person.id}">
    </a>
  </td>
  <td align="right" width="120">
    <div style="width:120px">
    {if $c3 > 0}
      <span class="light_gray">
      {$c3} abs
      </span>
    {/if}

    {if $c5 > 0}
      <span class="gray">
      , {$c5} abțineri
      </span>
    {/if}
    </div>
  </td>
</table>

<div id="compass_vote_details_{$tagid}_{$person.id}" style="display:none">
</div>
