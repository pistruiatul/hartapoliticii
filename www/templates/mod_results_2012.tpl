{* Smarty *}

Lista pe scurt a candidaților la <b>
<a href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">
  {$college_name}</a></b>
<br>

{include file="electoral_college_candidates.tpl"
    candidates=$candidates
    compact=$compact}

<div class="big" style="margin: 20px 0 10px 0;">
Informații pe larg despre acest colegiu
<b><a class="orange" href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">
  <span>aici</span></b></a>.</div>


