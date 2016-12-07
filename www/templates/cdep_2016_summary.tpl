{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign="top">
      <div class="college_search">
        <img src="/images/cautare_colegiu.png" style="float:right; margin-top:-10px;">
        <h2>Alegeri 2016 - listele electorale pe județe</h2>

        <div class="small gray" style="line-height: 1.4em;margin: 10px 0 10px 0;">
          Colegiile electorale s-au dus. <a href="https://www.registrulelectoral.ro/" target="_blank">Caută-ți secția de votare</a>.
        </div>
      </div>

      <table width=970 cellspacing=15>
        <tr>
            {include file="elections_2016_counties.tpl"}
        </tr>
      </table>

      <table width=960>
        <td valign="top">
          Site-ul BEC pentru aceste alegeri este aici:
          <a href="http://parlamentare2016.bec.ro/">parlamentare2016.bec.ro</a>
        </td>
        <td valign="top">
          {literal}
            <a href="https://twitter.com/share" class="twitter-share-button" data-text="Caută colegiul din care faci parte după stradă, comună sau oraș" data-via="hartapoliticii">Tweet</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
          {/literal}
        </td>
        <td valign="top">
          {literal}
            <div class="fb-like" data-href="http://hartapoliticii.ro/?cid=cauta_colegiu" data-send="false" data-width="100" data-show-faces="true"></div>
          {/literal}
        </td>
      </table>
    </td>
  </tr>
</table>
