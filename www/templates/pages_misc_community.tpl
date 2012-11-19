{* Smarty *}


<div style="padding: 15px; width: 940px;">
  <div class="add_link_button" onclick="ec.showAddLinkForm()">
          adaugă link <img src="/images/plus.png" align="absmiddle">
        </div>
{include file="electoral_college_add_link_form.tpl"}

  <div class="big">
    <b>Resurse adăugate și votate de comunitatea Hărții Politicii</b>
  </div>
  <br>
{include file="news_list_ugc.tpl" news=$news}

</div>