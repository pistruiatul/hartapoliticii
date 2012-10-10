<?php

/**
 * Returns the site URL.
 * @return the site URL.
 */
function getSiteUrl() {
  return 'http://'.$_SERVER['HTTP_HOST']."/";
}

/**
 * Builds a URL using the params.
 * @return the new URL.
 */
function constructUrl($baseUrl, $params, $newParams=array()) {
  $baseUrl .= '?';

  $p = array_merge($params, $newParams);

  $index = 0;
  foreach ($p as $key => $value) {
    $baseUrl .= "{$key}={$value}";
	if(index<sizeof($p)){
      $baseUrl .= "&amp;";
	}
  	$index++;
  }
  
  return $baseUrl;
}

?>
