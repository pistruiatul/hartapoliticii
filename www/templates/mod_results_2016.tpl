{* Smarty *}

<div style="margin: 20px 0 10px 0;">
Candidat din {$college_name|ucwords} pentru
    {if ($cam=="D")}
      Cam. Deputaților
    {else}
      Senat
    {/if}
    pe
<a href="?cid=27&colegiul={$college_name|lower}&cam={$cam}">listele {$party_name}</a>.</div>
