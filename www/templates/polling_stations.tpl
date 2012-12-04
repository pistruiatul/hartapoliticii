{* Smarty *}
<script type="text/javascript" src="/js/mapx.js"></script>

<script type="text/javascript">
  var map_center = [ 44.4375, 26.105 ];
  var addresses = [];
</script>

<table width=970 cellspacing=10>
  <tr>
    <td width=700 valign="top" rowspan=2>

    <div class="polling_search_form">
      <form onsubmit="codeAddress(); return false;">
        Adresa unde locuiești:
        <input type="text" id="q" placeholder="introdu adresa aici...">
        <input type="submit" id="cauta" value="  Caută  ">
      </form>
      <div class="small" style="float:right"> <div class="small">
        <a href="#" onclick="$('#raporteaza').show()">Raportează o problemă</a></div></div>
      <div id="map_message">Caută o adresă anume sau un oraș.
      Markere nu
      sunt afișate dacă harta e prea de ansamblu.</div>
    </div>

      <div id=raporteaza style="display:none" class="brightred">
        Te rog trimite un email la vivi@hartapoliticii.ro cu link-ul
        de sub hartă și cu o explicație a care este problema.<br><br>
      </div>

    <div>
      <div id="map_div" style="height:450px; width:700px;"></div>
    </div>
    <div style="float:right;padding-top:17px;">
            <div class="fb-like" data-href="http://hartapoliticii.ro/?cid=sectii_votare" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false"></div>
    </div>
    <input type="text" id="permalink">
    <br><br>

    Pentru secțiile de votare din străinătate, vezi
      <a href="http://www.mae.ro/sectii-de-votare-referendum">harta de pe site-ul MAE</a>.
      <br><br>

    Sursa pentru aceste secții de votare:

      <div id="polling_stations_source">
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7314">Alba</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7513">Arad</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7316">Argeș</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7515">Bacău</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7318">Bihor</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7401">Bistrița Năsăud</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7432">Botoșani</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7321">Brașov</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7409">Brăila</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7323">Buzău</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7324">Caraș Severin</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7325">Călărași</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7427">Cluj</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7399">Constanța</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7327">Covasna</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7402">Dâmbovița</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7329">Dolj</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7330">Galați</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7418">Giurgiu</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7508">Gorj</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7333">Harghita</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7509">Hunedoara</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7335">Ialomița</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7510">Iași</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7337">Ilfov</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7511">Maramureș</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7339">Mehedinți</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7429">Mureș</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7514">Neamț</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7342">Old</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7343">Prahova</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7406">Satu Mare</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7345">Sălaj</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7346">Sibiu</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7417">Suceava</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7407">Teoeirman</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7349">Timiș</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7350">Tulcea</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7351">Vaslui</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7408">Vâlcea</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7512">Vrancea</a>
        <a href="http://www.roaep.ro/ro/getdocument.php?id=7469">București</a>
        </div>
    </td>

    <td width="270" valign="top">
      <div id="sv">
        Caută adresa unde locuiești pentru a găsi secțiile de votare din
        jurul ei.
        <br><br>
        Dă click pe numărul unei secții de votare pentru a vedea ce străzi
        aparțin de fiecare.
        <br><br>
        <div class="brightred">Deși majoritatea secțiilor de votare sunt pe hartă,
          unele pot fi poziționate eronat.<br><br>
          Dacă găsești probleme te rog
          <a href="#" onclick="$('#raporteaza').show()">raportează-le</a> și în felul ăsta vom
          îmbunătăți harta pentru toată lumea.
          </div>

      </div>


    </td>
  </tr>
</table>
