{* Smarty *}

<div class="sidemoduletitle">
  Alte legături
</div>

<ul class="identity_ul">
  <li class="identity_li small">
    <a href="http://www.google.ro/search?hl=ro&q={$title}&meta=lr%3Dlang_ro">
      Caută pe google "{$title}"
    </a>
  </li>

  <li class="identity_li small">
    <a href="http://www.google.ro/search?hl=ro&q={$title}+site:wikipedia.org&btnI=Mă Simt Norocos&meta=lr%3Dlang_ro">
      Caută pe Wikipedia
    </a>
  </li>

  <li class="identity_li small">
    <a href="javascript:togglePhotoSuggestForm();">
      <img src="images/plus.png" border=0> Propune o fotografie
    </a>
    {include file="person_add_a_photo.tpl"}
  </li>
</ul>

