{* Smarty *}

<div style="padding-left:10px">
  <div class="search_declarations_panel">
    <form action="/" method="GET">
      <input type=hidden name="name" value="{$name}">
      <input type=hidden name="exp" value="person_declarations">
      <input type=hidden name="text_mode" value="{$text_mode}">

      <input type=text size=30 name=dq value="{$dq}"
             id="declarations_q">
      <input type=submit value="Caută" id="cauta">
    </form>
    <div class="small gray">
      Exemplu: "pensii", "taxe", etc
    </div>
  </div>
  <table width=100% cellspacing=2 cellpadding=2>
    {section name=n loop=$declarations}
    {strip}
      <tr>
      <td valign="top" width="20">
        <span class="light_gray">
          {$smarty.section.n.index+1+$start}.
        </span>
      </td>
      <td valign="top">

       <div class="small declaration_snippet">
         <div class="declaration" id="declaration-{$declarations[n].id}">
           {$declarations[n].snippet}
         </div>
         <div class="declaration_source">

           <span class="medium gray">
               {$declarations[n].time|date_format:"%d&nbsp;%b"}&nbsp;
               {$declarations[n].time|date_format:"%Y"}
           </span>
           &nbsp;
           <img src="images/popout_icon.gif" border="0"
                width="12" height="12" hspace="5">&nbsp;

           <span>
             Sursa: <a href="{$declarations[n].source}">stenograme parlament</a>
           </span>
         </div>
         </a>
       </div>
      </td>
      </tr>
    {/strip}
    {/section}
  </table>

  <table width="100%">
    <td>
      {if !$first_page}
        <a href="{$prev_page_link}">Prev</a>
      {else}
        Prev
      {/if}

      /

      {if !$last_page}
        <a href="{$next_page_link}">Next</a>
      {else}
        Next
      {/if}
    </td>
    <td align="right">
      <!--
      Uncomment this when and if we want to enable snippets.
      {if $text_mode == 'full_text'}
        Full text
      {else}
        <a href="{$full_text_link}">Full text</a>
      {/if}

      /

      {if $text_mode == 'snippets'}
        Snippets
      {else}
        <a href="{$snippets_link}">Snippets</a>
      {/if}
      -->
    </td>
  </table>
</div>

<script type="text/javascript">
  // Initializes the select handlers on the declarations on this page. This
  // Means that when the user selects some text they can mark it as important
  // or interesting.
  declarations.initSelectHandlers();
  {literal}
  declarations.globalRanges['declaration-126'] = [{ 'start': 30, 'end': 50 }];
  {/literal}
  declarations.refreshDeclaration(126);
</script>
