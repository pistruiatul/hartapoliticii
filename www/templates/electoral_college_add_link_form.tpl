{* Smarty *}

<div class="add_link_form" style="display:none">
  <b>Adaugă o resursă informativă utilă:</b><br><br>
  <input type="text" size="20" name="link" id="link_input"
          placeholder="adaugă link aici...">

  <input type="submit" value="  Adaugă  "
         id="submit_button"
         onclick="ec.addLink()">

  <div id="link_add_message"></div>

  <div class="medium gray">
    Acest link va fi procesat de către harta politicii pentru a determina
    că într-adevăr se referă la un candidat din acest colegiu, și abia apoi
    adăugat ca resursă de informare pe acestă pagină.

  </div>
</div>