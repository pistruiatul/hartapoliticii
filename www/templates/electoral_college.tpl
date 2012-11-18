{* Smarty *}

<table width=970 cellspacing=15>
  <td>
    <div style="float:right">
      <a href="/?cid=cauta_colegiu">Caută alt colegiu uninominal</a>
    </div>
    <div class="big"><b>Colegiul uninominal {$college_name}</b></div>
    <br>
    {if $college_image}
        <img src="{$college_image|lower|replace:' ':'_'}"
        style=" border: 5px solid #eee;" width="960" height="150">
        <br><br>
    {else}
      <iframe seamless="seamless"
              scrolling="no" frameboder="0"
              style="width: 960px; height: 150px; border: 5px solid #eee;"
              src="http://www.politicalcolours.ro/integrate.html?id={$pc_id}&p1={$pc_county_short}&p2={$pc_number}"></iframe>
      <div class="powered_by">
        hartă oferită de
        <a href="http://politicalcolours.ro/" target="_blank">politicalcolours.ro</a>
      </div>
    {/if}
    <table width="970">
      <td width="400" valign="top">
        <span class="medium"><b>Candidați 2012</b> - alfabetic</span>
        <br><br>
        {include file="electoral_college_candidates.tpl"
            candidates=$candidates_2012}
      </td>
      <td width="570" valign="top">
        <div style="margin-left:30px">
        {if count($links)>0}
          <span class="medium"><b>Resurse adăugate de utilizatori</b></span>
          <br><br>
        {else}
          <div style="float:right;display:none">
            Adaugă și tu un link
          </div>
        {/if}

        {include file="electoral_college_add_link_form.tpl"}

        {if count($news)>0}
          <span class="medium"><b>Știri recente cu candidații din acest colegiu</b></span>
          <br><br>
          {include file="news_list_wide.tpl" news=$news}
          <a href="/?cid=14&sid=0&college_restrict={$college_name|lower|replace:' ':'+'}&year=2012">Vezi toate știrile...</a>
        {else}
          <br><br>
          <center>Încă nu avem știri despre candidații din acest colegiu.</center>
        {/if}
        </div>
      </td>
    </table>


    <br><br>
    <span class="big">
      <b>Rezultate alegeri 2008</b>
    </span>
    <br><br>
    {include file="electoral_college_results.tpl"
        candidates=$candidates_2008
        id_winner=$id_winner_2008
        show_minorities_link=$show_minorities_link}
    <br><br>

    <span class="smalltitle">
      <b>Ce include acest colegiu</b>
    </span>

    <div class="ec_description">
      {section loop=$descriptions name=d}
        {if $pc_id==15}
          <div>{$descriptions[d]}</div>
        {else}
          <div>
            <a href="/?cid=search&q={$descriptions[d]}">{$descriptions[d]}</a></div>
        {/if}
      {/section}
    </div>

    <div class="medium gray">
      Sursa:
      <a href="{$description_source}" target="_blank">site-ul BEC</a>.
    </div>
  </td>
</table>