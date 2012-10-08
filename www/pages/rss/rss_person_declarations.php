<?php

include_once('hp-includes/rss_class.php');
include_once('hp-includes/person_class.php');

$id = $_GET['id'];

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
	
  
  $rssTitle = "Declaratii ".$person->displayName;
  $rssLinkPath = "?name={$person->getNameForUrl()}&exp=person_declarations";
  $rssDescription = "Fluxul declarațiilor lui ".$person->displayName;
  $rss = new Rss($rssTitle,$rssLinkPath,$rssDescription);
  
  $t = new Smarty();
  
  $rss2['title'] = "Declaratii ".$person->displayName;
  $rss2['link'] = $site_url."?name={$person->getNameForUrl()}&exp=person_declarations";
  $rss2['description'] = "Fluxul declarațiilor lui ".$person->displayName;

  foreach ($declarations as $declaration) {
  		
  	$date = gmdate(DATE_RSS, strtotime($declaration['time'].time()));
    $rss2_item['title'] = "Declaratie ".$person->displayName." la data de ".$date;
    $rss2_item['description'] = substr($declaration['declaration'], 0, 250)."...";
	$rss2_item['link'] = constructUrl($site_url, array(), array(
	    'name' => $site_url.$person->getNameForUrl(),
    	'exp' => 'person_declarations',
    	'decl_id' => $declaration['id']
  	));
    $rss2_item['pubDate'] = $date;
    $rss2_items[] = $rss2_item;
	
	
	$rss->addRssItem()
	
  }
  $t->assign('rss', $rss2);
  $t->assign('rss_items', $rss2_items);

  $t->display('rss.tpl');
  //http://www.phpeveryday.com/articles/Smarty-Variable-Associative-Arrays-P611.html
}

?>
