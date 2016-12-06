{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign="top">
      <div class="college_search">
        <img src="/images/cautare_colegiu.png" style="float:right; padding: 10px 0 30px 0;">
        <br>
        Pe 11 Decembrie 2016 se țin alegeri parlamentare - înapoi la voturi pe listă

        <div class="small gray" style="line-height: 1.4em;margin: 10px 0 10px 0;">
          Colegiile electorale s-au dus. <a href="/?cid=sectii_votare">Caută-ți secția de votare</a>.
        </div>

      </div>

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

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign="top">

      <table cellpadding=5 cellspacing=0>
      {foreach from=$colleges item=college key=county name=counties}
        {if $smarty.foreach.counties.index % 4 == 0}
          <tr>
        {/if}
          <td width="300" valign="top">
            {$county|ucwords}:
            <a href="?cid=27&colegiul={$county|lower|replace:' ':'+'}&cam=S">Sen</a> {$college.seats[0]},
            <a href="?cid=27&colegiul={$county|lower|replace:' ':'+'}&cam=D">C. Dep</a> {$college.seats[1]}
          </td>

      {/foreach}
      <table>

    </td>
  </tr>
</table>