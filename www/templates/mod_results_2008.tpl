{* Smarty *}

Rezultate <b>
<a href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">
  {$college_name}</a></b>

<br><br>

{include file="electoral_college_results.tpl"
    candidates=$candidates
    id_winner=$id_winner
    show_minorities_link=$show_minorities_link}


