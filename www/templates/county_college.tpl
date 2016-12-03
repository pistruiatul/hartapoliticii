{* Smarty *}
<script type="text/javascript" src="http://hartapoliticii.ro/clickheat/js/clickheat.js"></script><noscript><p><a href="http://www.dugwood.com/index.html">CMS</a></p></noscript><script type="text/javascript"><!--
clickHeatSite = 'hartapoliticii';clickHeatGroup = 'electoral_college';clickHeatServer = 'http://hartapoliticii.ro/clickheat/click.php';initClickHeat(); //-->
</script>

<table width=970 cellspacing=15>
  <td>
    <div style="float:right">
      <a href="/?cid=sectii_votare">Caută secția ta de votare după adresă</a>
    </div>

    <div>Alegeri în județul {$county_name}:
    <a href="?cid=27&colegiul={$county_name|lower|replace:' ':'+'}&cam=S">Senat</a> /
    <a href="?cid=27&colegiul={$county_name|lower|replace:' ':'+'}&cam=D">Camera Deputaților</a>
    </div>
    <br>

    <table width="970" style="margin-top:12px">
      <!-- Running in 2016 -->

      <tr>
        <td width="970" valign="top" colspan=2>

          <div class="big" style="margin-bottom:12px">Candidați 2016 - <b>{$county_name}, {$cam}</b></div>
          <table width="940" style="margin-top: 8px;">
            <tr>
            {section name=i loop=$parties}
            {strip}
              {if ($smarty.section.i.index) % 3 == 0}
                </tr><tr>
              {/if}
              <td valign="top">
                  <div class="module" style="xbackground-color: #F3F6FF">
                    <b>{$parties[i]->longName}</b> - {$parties[i]->lists[$college_name]|@count} candidați

                    {include file="county_college_people_list.tpl" people=$parties[i]->lists[$college_name]}
                  </div>
              </td>

            {/strip}
            {/section}

          </table>

          <!-- the buttons to add content, like, tweet, etc -->

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

      <!-- section for news -->

      <tr>
      <td colspan=2>
        {include file="electoral_college_add_link_form.tpl"}
      </td>
      </tr>
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

  </td>
</table>
<link rel="stylesheet" href="//libs.cartocdn.com/cartodb.js/v2/themes/css/cartodb.css" />
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="//libs.cartocdn.com/cartodb.js/v2/cartodb.js"></script>
