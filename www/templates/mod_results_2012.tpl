{* Smarty *}

Candidează la <b>
<a href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">
  Colegiul {$college_name}</a></b>, împreună cu următorii:
<br>

{include file="electoral_college_candidates.tpl"
    candidates=$candidates
    compact=$compact}



