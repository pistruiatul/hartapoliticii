{* Smarty *}

<div class="sidemoduletitle">
  Reprezentant {$college_name}
</div>

<iframe seamless="seamless"
        scrolling="no" frameboder="0"
        style="width: 290px; height: 150px; border: 5px solid #eee;"
        src="http://www.politicalcolours.ro/integrate.html?id={$pc_id}&p1={$pc_county_short}&p2={$pc_number}"></iframe>
<div style="float:left; font-size: 83%">
  <a href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">{$college_name}...</a></div>

<div class="powered_by">
  hartă
  <a href="http://politicalcolours.ro/" target="_blank">politicalcolours.ro</a>
</div>