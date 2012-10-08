<?php

include_once('hp-includes/url_functions.php');

/**
 * @fileoverview A class used for generating RSS feeds. This class will contain utilities
 * for generating RSS feeds in a generic way.
 */
class Rss {

  /** The rss title. */
  public $title;

  /** The rss link. */
  public $link;

  /** The rss description. */
  public $description;
  
  public $rssItems = array();
  
  public function __construct($title,$linkPath,$description){
    $this->title=$title;
    $this->link=getSiteUrl().$linkPath;
    $this->description=$description;
  }
  
  public function addRssItem($rssItemTitle,$rssItemDescription,$rssItemLink,$rssItemPubDate){
	$rssItem['title'] = trim($rssItemTitle);
	$rssItem['description'] = substr(trim($rssItemDescription), 0, 250)."...";;
	$rssItem['link'] = $rssItemLink;
	$rssItem['pubDate'] = $rssItemPubDate;
	$this->rssItems[] = $rssItem;
  }
  
  public function getTitle(){
  	return $this->title;
  }

  public function getLink(){
  	return $this->link;
  }

  public function getDescription(){
  	return $this->description;
  }

  public function getRssItems(){
  	return $this->rssItems;
  }

  /**
   * Prints the RSS feed using a Smarty template.
   */
  public function printRssSmarty(){
  	$smarty = new Smarty();
  	$smarty->assign('rss', $this);
  	$smarty->display('rss.tpl');
  }

}
?>
