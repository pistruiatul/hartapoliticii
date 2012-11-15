<?php
// If you are accessing this page directly, redirect to the front page
if (!$DB_USER) {
  header('Location: http://hartapoliticii.ro/');
}
$title = "Informații obiective despre politicieni și alegeri";

?>
<meta property='og:site_name' content='Informații obiective despre politicieni și alegeri - Harta Politicii din România' />
<meta property='og:website' content='http://hartapoliticii.ro' />
<meta property='og:title' content='Informații obiective despre politicieni și alegeri - Harta Politicii din România' />
<meta property='og:image' content='http://hartapoliticii.ro/top_title.png' />
<meta property='fb:app_id' content='205183855930' />

<!-- Update your html tag to include the itemscope and itemtype attributes -->
<html itemscope itemtype="http://schema.org/Organization">

<!-- Add the following three tags inside head -->
<meta itemprop="name" content="Informații obiective despre politicieni și alegeri - Harta Politicii din România">
<meta itemprop="description" content="Punctul tău de referință pentru politicieni Români. Află cum au votat, cum au fost aleși, unde au fost menționați.">
<meta itemprop="image" content="http://hartapoliticii.ro/top_title.png">
<?php

include('header.php');
include('hp-includes/people_lib.php');

// And now here I should put some content, like something about the elections,
// some stats, some news, something like that.

$page = 'home/page';
include('pages/submenu.php');

?>
