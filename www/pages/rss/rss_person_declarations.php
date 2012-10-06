<?php

include_once('hp-includes/person_class.php');

$id = $_GET['id'];
$site_url = 'http://'.$_SERVER['HTTP_HOST']."/";

// Given the ID of a person, go through it's history and load all
// the mods (modules) in the respective directories.
$person = new Person();
$person->setId($id);
$person->loadFromDb();

$declarations = $person->searchDeclarations('', 0, 10, false, 'all');

function constructUrl($baseUrl, $params, $newParams=array()) {
  $baseUrl .= '?';

  $p = array_merge($params, $newParams);

  foreach ($p as $key => $value) {
    $baseUrl .= "{$key}={$value}&";
  }

  return $baseUrl;
}

if (sizeof($declarations) > 0) {
  $t = new Smarty();
  
  $rss['title'] = "Declaratii ".$person->displayName;
  $rss['link'] = $site_url."?name={$person->getNameForUrl()}&exp=person_declarations";
  $rss['description'] = "Fluxul declarațiilor lui ".$person->displayName;

  foreach ($declarations as $declaration) {
  	$date = gmdate(DATE_RSS, strtotime($declaration['time'].time()));
    $rss_item['title'] = "Declaratie ".$person->displayName." la data de ".$date;
    $rss_item['description'] = substr($declaration['declaration'], 0, 250)."...";
	$rss_item['link'] = constructUrl($site_url, array(), array(
	    'name' => $site_url.$person->getNameForUrl(),
    	'exp' => 'person_declarations',
    	'decl_id' => $declaration['id']
  	));
    $rss_item['pubDate'] = $date;
    $rss_items[] = $rss_item;
  }
  $t->assign('rss', $rss);
  $t->assign('rss_items', $rss_items);

  $t->display('rss.tpl');
  //http://www.phpeveryday.com/articles/Smarty-Variable-Associative-Arrays-P611.html
}

?>
