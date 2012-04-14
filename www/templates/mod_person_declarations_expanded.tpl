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

  <div class="please_login_panel">
  {if !$logged_in}
    Dacă <a href="/wp-login.php?action=login">te autentifici</a> vei putea
    să selectezi pasaje de declarații pentru a le marca drept importante.

  {else}
    Selectează pasajele care crezi că sunt importnate. Pentru a șterge un
    pasaj marcat important din greșală, dă click pe el.
  {/if}
  </div>

  <div class="declaration_types_menu">
    {if $decl_type == 'all' && $decl_id == 0}Toate{else}
        <a href="{$all_declarations_link}">Toate</a>
    {/if}
    &nbsp;/&nbsp;
    {if $decl_type == 'important' && $decl_id == 0}Doar cele importante{else}
        <a href="{$important_declarations_link}">Doar cele importante</a>
    {/if}
    &nbsp;/&nbsp;
    {if $decl_type == 'mine' && $decl_id == 0}Marcate de mine{else}
        <a href="{$my_declarations_link}">Marcate de mine</a>
    {/if}
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

       <div class="small declaration_snippet {$declarations[n].class}">
         <div class="declaration" id="declaration-{$declarations[n].id}">
           {$declarations[n].snippet}
         </div>
         <table width="100%">
           <td>
             <div class="declaration_source">

               <span class="medium gray">
                   {$declarations[n].time|date_format:"%d&nbsp;%b"}&nbsp;
                   {$declarations[n].time|date_format:"%Y"}
               </span>
               &nbsp;
               <img src="images/popout_icon.gif" border="0"
                    width="12" height="12" hspace="5">&nbsp;

               <span>
                 Sursa: <a href="{$declarations[n].source}">
                     stenograme parlament</a>
               </span>
             </div>
           </td>
           <td align="right">
             <a href="{$declarations[n].link_to}">
               <img src="/images/link_icon.jpeg" border="0">
             </a>
           </td>
         </table>
       </div>
      </td>
      </tr>
    {/strip}
    {/section}
  </table>

  <table width="100%">
    <td align="right">
      <!--
      Uncomment this when and if we want to enable snippets.
      {if $text_mode == 'full_text'}Full text{else}
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

    <td align="right">
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
  </table>
</div>

<script type="text/javascript">
  // Initializes the select handlers on the declarations on this page. This
  // Means that when the user selects some text they can mark it as important
  // or interesting.
  declarations.initSelectHandlers({$logged_in});

  // insert all the user's ranges and the global ranges here.
  {section name=gr loop=$highlights_global_ranges}
  {strip}
    declarations.globalRanges[
        'declaration-{$highlights_global_ranges[gr].declarationId}'] =
    declarations.globalRanges[
        'declaration-{$highlights_global_ranges[gr].declarationId}'] || [];

    declarations.globalRanges[
        'declaration-{$highlights_global_ranges[gr].declarationId}'].push(
        {literal}
        {
        {/literal}
          'start': {$highlights_global_ranges[gr].start},
          'end': {$highlights_global_ranges[gr].end}
        {literal}
        }
        {/literal}
        );
  {/strip}
  {/section}
  // insert all the user's ranges and the global ranges here.
  {section name=mr loop=$highlights_my_ranges}
  {strip}
    declarations.myRanges[
        'declaration-{$highlights_my_ranges[mr].declarationId}'] =
    declarations.myRanges[
        'declaration-{$highlights_my_ranges[mr].declarationId}'] || [];

    declarations.myRanges[
        'declaration-{$highlights_my_ranges[mr].declarationId}'].push(
        {literal}
        {
        {/literal}
          'start': {$highlights_my_ranges[mr].start},
          'end': {$highlights_my_ranges[mr].end}
        {literal}
        }
        {/literal}
        );
  {/strip}
  {/section}

  declarations.refreshAllDeclarations();
</script>
