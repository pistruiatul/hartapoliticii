{* Smarty *}

<table width=970 cellspacing=15>

  <tr>
   <td align="right">
     <script type="text/javascript">
       {literal}
       function addSearch() {
          var voteId = document.getElementById('idv').value;
          {/literal}
          document.location.href = "/?cid={$cid}&sid={$sid}&q=" + voteId;
          {literal}
       }
       {/literal}
     </script>

     Caută după ID-ul votului:
     <input type="text" size="6" name="idv" id="idv">
     <input type="Submit" value=" go " onclick="addSearch();">
   </td>
  </tr>

  <tr>
    <td width=970 valign=top>
      <table width=100% cellspacing=2>
      <tr>
        <td colspan="3">
          <p class="smalltitle">
            <strong>
              Voturile din camera deputatilor
            </strong>
          </p>
        </td>
        <td>DA</td>
        <td>NU</td>
        <td>Ab</td>
        <td>-</td>
      </tr>
      {section name=mrvc loop=$votes}
      {strip}
        <tr>
          <td valign="top">{$from+$smarty.section.mrvc.index+1}.</td>
          <td valign="top" width=120>
            <a href="{$votes[mrvc].link}">
              {$votes[mrvc].type}
            </a><br>
            <div class="small">
              {$votes[mrvc].time|date_format:"%d %b %Y, %H:%M"}
            </div>
          </td>
          <td valign="top">
            <div class="medium">
              {$votes[mrvc].subject}
            </div>
            {include file="parl_tagged_vote.tpl" idvote=$votes[mrvc].id
                room="cdep"  year="2008" tags=$votes[mrvc].tags}
          </td>

          <td valign="top">{$votes[mrvc].vda}</td>
          <td valign="top">{$votes[mrvc].vnu}</td>
          <td valign="top">{$votes[mrvc].vab}</td>
          <td valign="top">{$votes[mrvc].vmi}</td>
        </tr>
      {/strip}
      {/section}
      </table>
    </td>
  </tr>

  <tr>
   <td align="right">
     {if $fromPrev>0}
       <a href="?cid={$cid}&sid={$sid}&from={$fromPrev}">Previous</a>
     {else}
       Previous
     {/if}
     |

     <a href="?cid={$cid}&sid={$sid}&from={$fromNext}">Next</a>
   </td>
  </tr>
</table>
