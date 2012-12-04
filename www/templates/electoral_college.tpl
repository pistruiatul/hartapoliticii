{* Smarty *}
<script type="text/javascript" src="http://hartapoliticii.ro/clickheat/js/clickheat.js"></script><noscript><p><a href="http://www.dugwood.com/index.html">CMS</a></p></noscript><script type="text/javascript"><!--
clickHeatSite = 'hartapoliticii';clickHeatGroup = 'electoral_college';clickHeatServer = 'http://hartapoliticii.ro/clickheat/click.php';initClickHeat(); //-->
</script>

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
        <br>
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

    <table width="970" style="margin-top:12px">
      <tr>
      <td width="970" valign="top" colspan=2>
        <div style="float:right">
          <a href="/?cid=sectii_votare">Caută-ți secția de votare</a>
        </div>
        <div class="big" style="margin-bottom:12px"><b>Candidați 2012</b> - alfabetic</div>

        <div class="module" style="background-color: #F3F6FF">
        {include file="electoral_college_candidates_wide.tpl"
            candidates=$candidates_2012}
        </div>

        <table width="940" style="margin-top: 8px;">
          <td width="40">
            <div class="add_link_button" onclick="ec.showAddLinkForm()">
              Adaugă link <img src="/images/plus.png" align="absmiddle">
            </div>
          </td>
          <td width="890" align="right">Recomandă pagina asta și altora:

            <div class="fb-like" data-href="http://hartapoliticii.ro/?cid=23&colegiul={$college_name|lower|replace:' ':'+'}" data-send="false" data-layout="button_count" data-width="200" data-show-faces="true" data-action="recommend"></div>
            <a href="https://twitter.com/share" class="twitter-share-button"  data-url="http://hartapoliticii.ro/?cid=23&colegiul={$college_name|lower|replace:' ':'+'}" data-text="Eu votez la colegiul uninominal {$college_name}" data-via="hartapoliticii" data-hashtags="alegeri2012">Tweet</a>
            {literal}
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            {/literal}
          </td>

        </table>
      </td>
      </tr>

      <tr>
      <td colspan=2>
      {include file="electoral_college_add_link_form.tpl"}
      </td>
      <tr>
      {if count($links)>0}
      <td width="485" valign="top">
        <div>

          <div class="module" style="padding: 10px;">
          <div class="big" style="margin-bottom:10px">
            <b>Resurse adăugate de utilizatori</b></div>

          {include file="news_list_ugc.tpl" news=$links}

          <div class="small" style="text-align:right">
          <a href="/?cid=comunitate&college_restrict={$college_name|lower|replace:' ':'+'}">Vezi toate resursele...</a>
          </div>

          </div>
      </td>
      <td width="485" valign="top">
      {else}

      <td width="910" valign="top" colspan=2>
      {/if}
        <div class="module" style="padding: 10px;">
        {if count($news)>0}
          <div class="big" style="margin-bottom:10px">
            <b>Știri recente cu candidații din acest colegiu</b></div>

          {include file="news_list_wide.tpl" news=$news}

          <div class="small" style="text-align:right">
          <a href="/?cid=14&sid=0&college_restrict={$college_name|lower|replace:' ':'+'}&year=2012">Vezi toate știrile...</a>
          </div>
        {else}
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