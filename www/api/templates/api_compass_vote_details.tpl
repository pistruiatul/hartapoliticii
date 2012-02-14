{* Smarty *}

<table class="small" style="padding-left:20px;width:850px;" cellpadding="7">
  <tr>
    <td>Lege</td>
    <td></td>
    <td>Vot</td>
  </tr>

{section name=v loop=$votes}
  {strip}
    <tr>
      <td>
        <a href="{$votes[v].link}">
          {$votes[v].type}
        </a>: &nbsp;
        {$votes[v].description}
      </td>
      <td valign="top">
        {if $votes[v].inverse == 0}
          pentru
        {else}
          contra
        {/if}

      </td>
      <td valign="top">
        {if $votes[v].vote}
          <b>{$votes[v].vote}</b>
        {else}
          absent
        {/if}
      </td>
    </tr>
  {/strip}
{/section}
</table>
