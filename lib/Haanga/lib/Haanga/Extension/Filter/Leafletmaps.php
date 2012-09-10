<?php

class Haanga_Extension_Filter_Leafletmaps{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$randId = rand();
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$north = -90; $south=90; $east=-180; $west = 180;
  	$latArr =""; $longArr=""; $nameArr = "";
  	$options = array();
  	$options['width'] = 500;
  	$options['height'] = 500;
  	$options['zoom'] = 10;
  	for($z=3; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }
  	$w = $options['width'];
  	$h = $options['height'];
  	$z = intval($options['zoom']);

  	$points = array();
  	foreach($obj as $k){
  	  $currentPoint = array();
  	  if($north < $k->$names[0]->value){
  	  	$north = $k->$names[0]->value;
  	  }
  	  $currentPoint['lat'] = $k->$names[0]->value;
  	  if($south > $k->$names[0]->value){
  	  	$south = $k->$names[0]->value;
  	  }
  	  
  	  if($west > $k->$names[1]->value){
  	  	$west = $k->$names[1]->value;
  	  }
  	  if($east < $k->$names[1]->value){
  	  	$east = $k->$names[1]->value;
  	  }  	  
  	  $currentPoint['long'] = $k->$names[1]->value;
  	  
  	  $currentPoint['label'] = $k->$names[2]->value;
  	  $firstColumn = false;
  	  array_push($points, $currentPoint);
  	}
  	
  	$centerLat = ($south+$north)/2;
  	$centerLon = ($east+$west)/2;
  	$pre = "<div id='map_$randId' style='height: 580px;'></div>
  	<script src='http://cdn.leafletjs.com/leaflet-0.4/leaflet.js'></script>
  	<script type='text/javascript'>
    //<![CDATA[
    
    function loadCssFile() {
     var fileref=document.createElement('link');
     fileref.setAttribute('rel', 'stylesheet');
     fileref.setAttribute('type', 'text/css');
     fileref.setAttribute('href', 'http://cdn.leafletjs.com/leaflet-0.4/leaflet.css');
     document.getElementsByTagName('head')[0].appendChild(fileref);
    }
    
    
    function initialize_$randId() {
 	   var locations = ".json_encode($points).";	  
	   var mapOptions = ".json_encode($options).";
	   mapOptions.attribution = osmAttrib;
	   
	   map = new L.Map('map_$randId');
	   var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	   var osmAttrib='Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors';
	   var osm = new L.TileLayer(osmUrl, mapOptions );			
	   map.setView([$centerLat, $centerLon],mapOptions.zoom);
	   map.addLayer(osm);
	   
	   for(var i=0;i<locations.length;i++){
	     L.marker([locations[i].lat, locations[i].long]).addTo(map).bindPopup(locations[i].label)
	   }
	  }

//    loadCssFile();
    initialize_$randId();
    //]]>
    </script>";
    return $pre;
  }
}
