{* Smarty *}

<table width=970 cellspacing=15>
  <td>
    <div style="float:right">
      <a href="/?cid=cauta_colegiu">Caută alt colegiu uninominal</a>
    </div>
    <div class="big">Colegiul electoral <b>{$college_name}</b></div>
    <br>
    <iframe seamless="seamless"
            scrolling="no" frameboder="0"
            style="width: 960px; height: 150px; border: 5px solid #eee;"
            src="http://www.politicalcolours.ro/integrate.html?id={$pc_id}&p1={$pc_county_short}&p2={$pc_number}"></iframe>
    <div class="powered_by">
      hartă oferită de
      <a href="http://politicalcolours.ro/" target="_blank">politicalcolours.ro</a>
    </div>
    <br>
    În curând vom afișa aici mai multe informații despre acest colegiu. <br>
    Până atunci, iată rezultatele de la alegerile din 2008.

    <br><br><br>
    <span class="smalltitle">
      <b>Rezultate alegeri 2008</b>
    </span>
    <br>
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