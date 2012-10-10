<?php

include_once('hp-includes/url_functions.php');

/**
 * A class used for generating RSS feeds. This class will contain 
 * utilities for generating RSS feeds in a generic way.
 */
class Rss {

  /** The rss title. */
  public $title;

  /** The rss link. */
  public $link;

  /** The rss description. */
  public $description;
  
  /** The URL that points to the origin of the RSS feed. */
  public $atomLinkSelf;
  
  /** The list of rss items. */
  public $rssItems = array();
  
  /** 
   * The constructor of the RSS class. 
   * 
   * @param $title the title of the page that generates the rss.
   * @param $linkPath the link of the page that generates the rss.
   * @param $description the description of the page.
   * @param $atomLinkSelf the url that point to the RSS feed.
   */
  public function __construct($title,$linkPath,$description,$atomLinkSelf){
    $this->title=$title;
    $this->link=getSiteUrl().$linkPath;
    $this->description=$description;
	$this->atomLinkSelf = $atomLinkSelf;
  }
  
  /** 
   * This method adds a new RSS item to the RSS channel. 
   * 
   * @param $rssItemTitle the title of the new item in the rss.
   * @param $rssItemDescription the descripiton of the new item in the rss.
   * @param $rssItemLink the link of the new item in the rss.
   * @param $rssItemPubDate the date when the item was published.
   */
  public function addRssItem($rssItemTitle,$rssItemDescription,$rssItemLink,
        $rssItemPubDate){
	$rssItem['title'] = substr(trim($rssItemTitle), 0, 50)."...";
	$rssItem['description'] = substr(trim($rssItemDescription), 0, 250)."...";
	$rssItem['link'] = $rssItemLink;
	$rssItem['pubDate'] = $rssItemPubDate;
	$this->rssItems[] = $rssItem;
  }
  
  /** 
   * This method return the title of the rss channel. 
   * @return the title of the rss channel.
   */
  public function getTitle(){
  	return $this->title;
  }

  /** 
   * This method return the link of the rss channel. 
   * @return the link of the rss channel.
   */
  public function getLink(){
  	return $this->link;
  }

  /** 
   * This method return the description of the rss channel. 
   * @return the description of the rss channel.
   */
  public function getDescription(){
  	return $this->description;
  }

  /** 
   * This method return the item of the rss channel. 
   * @return the title of the rss channel.
   */
  public function getAtomLinkSelf(){
  	return $this->atomLinkSelf;
  }

  /** 
   * This method return the items of the rss channel. 
   * @return the items of the rss channel.
   */
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
