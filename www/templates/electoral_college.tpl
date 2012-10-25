{* Smarty *}

<table width=970 cellspacing=15>
  <td>
    <div class="big">Colegiul electoral <b>{$college_name}</b></div>

    <br><br>
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
      <b>Ce includea acest colegiul în 2008</b>
    </span>

    <br>
      {$description_2008}.
    <br>
    Pentru descrierea pe larg vizitați
    <a href="http://www.becparlamentare2008.ro/colegii_uninominale.html">site-ul BEC</a>.
    <br>
  </td>
</table>