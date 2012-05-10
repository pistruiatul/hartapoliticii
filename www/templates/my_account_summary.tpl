{* Smarty *}

<table width=970>
  <td valign=top>
  <div class="my_account">

    <div class="section">
      <div class="title">Profil personal ({$user_login})</div>
      <div class="content">
        Pentru a îți schimba parola și alte informații despre tine,
        <a href="wp-admin/profile.php">dă click aici</a>.
        <br>
        Pentru a ajunge din nou la această pagină, dă click pe numele tău de
        utilizator în partea din dreapta sus a site-ului.
      </div>
    </div>

    <div class="section">
      <div class="title">Anotații legi</div>
      <div class="content">
        Mai multe detalii despre anotarea de legi
        <a href="http://www.hartapoliticii.ro/?p=4042">aici</a>.

        <table width="100%">
        <tr>
          <td width="50%"><b>Taguri Senat</b></td>
          <td width="50%"><b>Taguri Camera Deputaților</b></td>
        </tr>

        <tr>
           <td width="50%" valign="top" class="medium">
           {include file="my_account_tags_list.tpl" tags=$senatTags room='senat'}

           </td>
           <td width="50%" valign="top" class="medium">
           {include file="my_account_tags_list.tpl" tags=$cdepTags room='cdep'}
           </td>
        </tr>

        <tr class="small">
          <td>
            <img src="images/plus.png">
            <a href="?cid=12&sid=2">Adaugă tag-uri</a> la Senat.
          </td>
          <td>
            <img src="images/plus.png">
            <a href="?cid=11&sid=2">Adaugă tag-uri</a> la Camera Deputaților.
          </td>
        </tr>
        </table>
      </div>
    </div>

    {if $user_is_admin}
      {include file="my_account_add_new_person.tpl"
          recent_people=$recent_people}

      {include file="my_account_add_position.tpl"}
    {/if}

  </div>
  </td>
</table>
