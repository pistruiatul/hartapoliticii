{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign=top>
      
      <table width=100% cellspacing=2>
      <tr>
        <td colspan="3"><div class="smalltitle"><strong>Cele mai recente chestiuni votate in Camera Deputatilor</strong></div></td>
        <td>DA</td>
        <td>NU</td>
        <td>Ab</td>
        <td>-</td>
      </tr>
      {section name=mrvc loop=$mostRecentVotes}
      {strip}
        <tr>
          <td valign="top">{counter name=mrvc}.</td>
          <td valign="top" width=120>
            <a href="{$mostRecentVotes[mrvc].link}">
              {$mostRecentVotes[mrvc].type}
            </a><br>
            <div class="small">
              {$mostRecentVotes[mrvc].time|date_format:"%d %b %Y, %H:%M"}
            </div>
          </td>
          <td valign="top">
            <div class="medium">
              {$mostRecentVotes[mrvc].subject}
            </div>
          </td>
          
          <td valign="top">{$mostRecentVotes[mrvc].vda}</td>
          <td valign="top">{$mostRecentVotes[mrvc].vnu}</td>
          <td valign="top">{$mostRecentVotes[mrvc].vab}</td>
          <td valign="top">{$mostRecentVotes[mrvc].vmi}</td>
        </tr>
      {/strip}
      {/section}
      </table>
    </td>
  </tr>
  
  <tr>
    <td align="right">
      <a href="?cid={$cid}&sid={$sidVotes}">vezi toate voturile...</a>
    </td>
  </tr>
</table>