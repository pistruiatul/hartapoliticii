{* Smarty *}

Candidat {$party} la președinție.

<div id="content_{$content_id}">
  {$details}
</div>

{if $displayEdit}
  <div id="edit_{$content_id}" style="display:none">
	  <textarea id="text_{$content_id}" rows="10" cols="70">{$details}</textarea>
	  <input type="button" value="udpdate" 
	         onclick="javascript:updateContent({$content_id});">
	</div>
	
	<a href="javascript:editField({$content_id});" class="small">edit</a>
	<div style="display:inline" id="status_{$content_id}"></div>
	<br>
{/if}

{include file="video_section.tpl"}

<div class="small">
  Înapoi la <a href="?c=alegeri+prezidentiale+2009&cid=13">lista de candidați</a>.
</div>