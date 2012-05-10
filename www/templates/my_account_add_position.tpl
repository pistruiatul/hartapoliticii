{* Smarty *}

<div class="section">
  <div class="title">Adaugă o nouă poziție</div>

  <div id="recent_people">
    <b>Person</b>

    <div id="new_position_searched_person"></div>
  </div>

  <br>
  <div class="content">
  <table>
    <tr>
      <td>
        <div class="form_field">
          Nume:
        </div>
        <div class="example">Ex: Elena Udrea</div>
      </td>
      <td>
        <input size="30" id="new_position_display_name">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">History what:</div>
        <div class="example">Ex: gov/ro</div>
      </td>
      <td>
        <input size="30" id="new_position_what" value="gov/ro">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">Poziție</div>
        <div class="example">Ex: Ministrul Agriculturii şi Dezvoltării Rurale</div>
      </td>
      <td>
        <input size="30" id="new_position_title">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">Url</div>
        <div class="example">Ex: http://www.gov.ro/daniel-constantin__l1a116988.html</div>
      </td>
      <td>
        <input size="30" id="new_position_url">
      </td>
    </tr>

    <tr>
      <td>
        <div class="form_field">Start time</div>
        <div class="example">Ex: 1336435200</div>
      </td>
      <td>
        <input size="30" id="new_position_start_time">
      </td>
    </tr>


    <tr>
      <td>
      </td>
      <td>
        <input type="Submit"
               value="  Adaugă  "
               onclick="profile.handleSubmitPosition();"
               id="new_position_submit">
        <div id="new_position_add_message"></div>
      </td>
    </tr>
  </table>
  </div>

</div>
