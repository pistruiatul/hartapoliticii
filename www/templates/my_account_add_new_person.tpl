{* Smarty *}

<div class="section">
  <div class="title">Adaugă o nouă persoană politică</div>

  <div id="recent_people">
    {include file="my_account_recently_added_people.tpl"
        recent_people=$recent_people}
  </div>

  <div class="content">
  Te rog, mai întâi verifică dacă nu cumva această persoană deja există.
  <br>
  În acest moment verificarea asta nu se face automat și aș vrea să
  evităm duplicatele pe cât posibil.
  <br><br>
  <table>
    <tr>
      <td>
        <div class="form_field">
          Numele normal (ordinea "Prenume Nume"):
        </div>
        <div class="example">Ex: Elena Udrea</div>
      </td>
      <td>
        <input size="30" id="person_display_name">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">Numele complet:</div>
        <div class="example">Ex: Elena Gabriela Udrea</div>
      </td>
      <td>
        <input size="30" id="person_name_all">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">URL poză (opțional)</div>
        <div class="example">Ex: http://vivi.ro/i/centrul_vechi.jpg</div>
      </td>
      <td>
        <input size="30" id="person_photo_url">
      </td>
    </tr>

    <tr>
      <td>
      </td>
      <td>
        <input type="Submit" value="  Adaugă  "
               onclick="profile.addPerson();" id="person_submit">
        <div id="person_add_message"></div>
      </td>
    </tr>
  </table>

  </div>
</div>
