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
      <div id="map_message">Caută o adresă anume sau un oraș.
      Markere nu
      sunt afișate dacă harta e prea de ansamblu.</div>
    </div>

    <div>
      <div id="map_div" style="height:450px; width:700px;"></div>
    </div>

    <input type="text" id="permalink">

    </td>

    <td width="270" valign="top">
      <div id="sv">
        Caută adresa unde locuiești pentru a găsi secțiile de votare din
        jurul ei.
        <br><br>
        Dă click pe numărul unei secții de votare pentru a vedea ce străzi
        aparțin de fiecare.
        <br><br>
        <div class="red">Deși majoritatea secțiilor de votare sunt pe hartă,
          unele pot fi poziționate eronat.</div>

      </div>


    </td>
  </tr>
</table>
