{* Smarty *}

<div class="sidemoduletitle">
  Reprezentant {$college_name}
</div>

<div id="cartoDb" data-name="{$college_name}" data-county="{$pc_county_id}" data-number="{$pc_number}"></div>
{literal}
<script id="cartoCSS" type="text/html">
  ###type##_2008 {
    polygon-fill: #3E7BB6;
    polygon-opacity: 0.1;
    line-width: 1;
    line-color: #FFF;
    line-opacity: 1;
    polygon-comp-op: src-over;
    [jud_id = ##county## ] {
      [col_nr = ##number##] {
        polygon-opacity: 0.6; 
        line-width: 2;
      }
    }
  }
</script>
{/literal}
<link rel="stylesheet" href="//libs.cartocdn.com/cartodb.js/v2/themes/css/cartodb.css" />
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="//libs.cartocdn.com/cartodb.js/v2/cartodb.js"></script>
<div style="float:left; font-size: 83%">
  <a href="?cid=23&colegiul={$college_name|replace:' ':'+'|lower}">{$college_name}...</a></div>

<div class="powered_by">
  hartă
  <a href="http://politicalcolours.ro/" target="_blank">politicalcolours.ro</a>
</div>