{* Smarty *}

<table width=970 cellspacing=15>
  <tr>
    <td width=970 valign="top">

      <div class="college_search">
        <img src="/images/cautare_colegiu.png">
        <br>
        Caută colegiul din care faci parte după stradă, comună sau oraș.

        <div class="small gray" style="line-height: 1.4em;margin: 10px 0 10px 0;">
          Notă: pentru orașe mici, de multe ori strada nu e listată.
          Pentru acestea încearcă să cauți în forma "oraș județ".
        </div>

        <form action="/">
          <input type=hidden name=cid value="search">
          <input type=hidden name=r value="cs">
          <input type=text name=q class=q size=30>
          <input type=submit class=submit value="Caută">
        </form>
        <br>

      </div>

      <table width=960>
        <td valign="top">
        <b>Data alegerilor: 9 Decembrie</b>.<br>

        Site-ul BEC pentru aceste alegeri este aici:
        <a href="http://www.becparlamentare2012.ro/">www.becparlamentare2012.ro</a>
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

{* include file="senat_2008_summary_news_buzz.tpl" *}
