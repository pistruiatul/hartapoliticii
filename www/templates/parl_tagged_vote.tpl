{* Smarty *}

<div class="small gray right">
  Acest vot este:

  {* Show the list of tags. In the future, this should be clickable so I can
     select all the votes that have a certain tag. That would be SO HOT. *}
  {section name=t loop=$tags}
	  {strip}
	    <div class="news_list_mention" id="tag_{$tags[t].id}">
        {if $tags[t].inverse==1}
          contra
        {else}
          pentru
        {/if}
        &nbsp;
	      {$tags[t].tag|escape:html}&nbsp;<span class="white">|</span>&nbsp;
        <a href="javascript:removeVoteTag('{$room}', '{$year}', '{$idvote}',
          '{$tags[t].tag|escape:javascript}', '{$tags[t].id}');">
          <b>x</b>
        </a>
	    </div>
	  {/strip}
  {/section}

  {* The plus image to add a new tag. *}
  <a href="javascript:toggleDiv('holder_{$idvote}');"
     title="Adaugă tag">
    <img alt="Adaugă tag" src="images/plus.png" border="0">
  </a>

  {* A div holder to put in the form to add a new tag. *}
  <div id="holder_{$idvote}" style="display:none">
  {if $is_user_logged_in}
    Acest vot este
    <select id="select_{$idvote}">
      <option value="0" selected>pentru</option>
      <option value="1">contra</option>
    </select>
    <input id="input_{$idvote}" size="20"/>
    <input type="button" value="Adaugă"
           onclick="addVoteTag('{$room}', '{$year}', '{$idvote}');"/>
  {else}
    Trebuie <a href="wp-login.php?action=login">să fii autentificat</a> ca să
    adaugi un tag.
  {/if}
  </div>
</div>
<hr size=1 color="#DDDDDD">
