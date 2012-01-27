{* Smarty *}

{section name=i loop=$people}
{strip}
  <tr>
    <td valign="top" class="medium">
      <a href="?name={$people[i].name}">
        {$people[i].display_name}
      </a>&nbsp;
      <span class="small gray">
        {$people[i].party_name}&nbsp;
        {$people[i].percent|string_format:"%.2f"}%&nbsp; 
      </span>
    </td>
  </tr>
{/strip}
{/section}