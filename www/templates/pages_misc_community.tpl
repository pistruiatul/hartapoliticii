{* Smarty *}


<div style="padding: 15px; width: 940px;">
  <div class="add_link_button" onclick="ec.showAddLinkForm()">
          adaugă link <img src="/images/plus.png" align="absmiddle">
        </div>
{include file="electoral_college_add_link_form.tpl"}

  <div class="big">
    <b>Resurse adăugate și votate de comunitatea Hărții Politicii</b>
  </div>

  <span class="gray">Ordonează după</span>:
    {if $sort=='time'}
      <a href="/?cid=comunitate">număr voturi</a> |
      recență
    {else}
      număr voturi |
      <a href="/?cid=comunitate&sort=1">recență</a>
    {/if}
  <br><br>
  <div class="bigger_news_list">
    {include file="news_list_ugc.tpl" news=$news}
  </div>
</div>
