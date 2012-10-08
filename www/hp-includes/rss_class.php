<?php

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
    $this->link=$this->getSiteUrl().$linkPath;
    $this->description=$description;
  }
  
  public function addRssItem(){
  	
  }
  
  /**
   * Returns the site url.
   * @return {string}
   */
  public function getSiteUrl() {
    return 'http://'.$_SERVER['HTTP_HOST']."/";
  }
  
  public function printRssSmarty(){
  	$smarty = new Smarty();
  }

}
?>
