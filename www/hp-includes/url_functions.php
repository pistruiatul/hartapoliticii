<?php

/**
 * Returns the site url.
 * @return {string}
 */
function getSiteUrl() {
  return 'http://'.$_SERVER['HTTP_HOST']."/";
}

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
