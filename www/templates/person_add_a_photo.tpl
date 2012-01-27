{* Smarty *}

<div id="suggest_photo" style="display:none">
  <form onSubmit="sendPhoto(); return false;"
        action="?cid=suggest" method=GET id="suggest_photo_form">
    <input type=text name=url
        value="adaugă url-ul fotografiei aici (http://...)"
        id="suggest_photo_input"
        onfocus="clearValue('suggest_photo_input',
                   'adaugă url-ul fotografiei aici (http://...)');"></input>
    <input type=submit value="Adaugă" />
    <input type=hidden id=ps_pid name=pid value="{$idperson}">
    <input type=hidden id=ps_type name=type value=photo>
  </form>
</div>
