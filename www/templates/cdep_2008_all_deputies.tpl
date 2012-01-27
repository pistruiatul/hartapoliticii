{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td>
      <span class="small">Atenție: Datele de prezență sunt alcătuite pe baza 
      tuturor voturilor electronice din Camera Deputatilor: voturi finale dar și
      amendamente, prezență, sau decizii organizatorice.</span>
    </td>
  </tr><tr>
    <td width=100% valign=top>
      <p class="smalltitle"><strong>Cei mai prezenți deputați</strong></p>
      <table width=100%>
      <tr>
        <td></td>
        <td><a href="/?cid=11&sid=1&sort=0">Nume</a></td>
        <td><a href="/?cid=11&sid=1&sort=1">Colegiu</a></td>
        <td><a href="/?cid=11&sid=1&sort=2">Partid</a></td>
        <td><a href="/?cid=11&sid=1&sort=3">Prezență</a></td>
        <td align="right"><a href="/?cid=11&sid=1&sort=4">Rebel</a></td>
      </tr>
      
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
            {$mostPresent[most].college}
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
          <td valign="top" align="right">
            {$mostPresent[most].maverick|string_format:"%.2f"}%
          </td>
        </tr>
      {/strip}
      {/section}
      </table>
    </td>
  </tr>
</table>
