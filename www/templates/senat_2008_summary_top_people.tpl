{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td colspan=2>
      <span class="small">Atentie: Datele de prezenta sunt alcatuite pe baza tuturor voturilor electronice
      din Senat: voturi finale dar si amendamente, prezenta, sau 
      decizii organizatorice.</span>
    </td>
  </tr><tr>
    <td width=465 valign=top>
      <div class="smalltitle"><strong>Cei mai prezenți senatori</strong></div>
      <table width=460>
      {section name=most loop=$mostPresent}
      {strip}
        <tr>
          <td valign="top">{counter name=most}.</td>
          <td valign="top">
            <a href="?name={$mostPresent[most].name}">
              {$mostPresent[most].display_name}
            </a>
          </td> 
          <td valign="top">
            <span class="small green_link">
              <a href="/?cid=17&id={$mostPresent[most].party_id}">
                {$mostPresent[most].party_name}
              </a>
            </span>
          </td>
          <td valign="top" width="140">
            {$mostPresent[most].percent|string_format:"%.2f"} %&nbsp; 
            <span class="small gray">din {$mostPresent[most].possible} voturi</span>
            <br>
            <div style="background:#44DD44;
                 display:inline-block;
                 width:{$mostPresent[most].percent|string_format:"%.0f"}%;
                 height:2px"></div>
            <div style="background:#F5F5F5;
                 display:inline-block;
                 width:{$mostPresent[most].left_percent|string_format:"%.0f"}%;
                 height:2px"></div>
          </td>
        </tr>
      {/strip}
      {/section}
      <tr>
      </table>
    </td>

    <td width=465 valign=top>
      <div class="smalltitle"><strong>Cei mai absenți senatori</strong></div>
      <table width=465>
      {section name=least loop=$leastPresent}
      {strip}
        <tr>
          <td>{counter name=least}.</td>
          <td>
            <a href="?name={$leastPresent[least].name}">
              {$leastPresent[least].display_name}</td> 
            </a>
          <td>
            <span class="small green_link">
              <a href="/?cid=17&id={$leastPresent[least].party_id}">
                {$leastPresent[least].party_name}
              </a>
            </span>
          </td>
          <td width="140">
            {$leastPresent[least].percent|string_format:"%.2f"} %&nbsp; 
            <span class="small gray">din {$leastPresent[least].possible} voturi</span>
            <br>
            <div style="background:#FF4444;
                 display:inline-block;
                 width:{$leastPresent[least].percent|string_format:"%.0f"}%;
                 height:2px"></div>
            <div style="background:#FAFAFA;
                 display:inline-block;
                 width:{$leastPresent[least].left_percent|string_format:"%.0f"}%;
                 height:2px"></div>
          </td>
        </tr>
      {/strip}
      {/section}
      <tr>
      </table>
    </td>
  </tr>
  <tr><td colspan=2 align="right">
        <a href="?c=camera+deputatilor+2009&cid=12&sid=1">vezi lista completa...</a>
      </td>
</table>
