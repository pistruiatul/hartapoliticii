{* Smarty *}

<table width=970>
  <td valign=top>
  <div class="plaintext">

    <b>Profil personal ({$user_login})</b>
    <div class="medium" style="margin-left: 20px;margin-bottom:20px">
      Pentru a îți schimba parola și alte informații despre tine,
      <a href="wp-admin/profile.php">dă click aici</a>.
      <br>
      Pentru a ajunge din nou la această pagină, dă click pe numele tău de
      utilizator în partea din dreapta sus a site-ului.
    </div>

    <hr size="1" style="color: rgb(221, 221, 221);"/>
    <b>Anotații legi</b>
    <div style="margin-left: 20px;">
      <table width="100%">
      <tr>
        <td width="50%">Taguri Senat</td>
        <td width="50%">Taguri Camera Deputaților</td>
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
      <br>
      Mai multe detalii despre anotarea de legi
      <a href="http://www.hartapoliticii.ro/?p=4042">aici</a>.
    </div>

  </div>
  </td>
</table>