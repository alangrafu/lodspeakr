<?php

class Haanga_Extension_Filter_GoogleMaps{
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
  	$w = "400";
  	$h = "300";
  	
  	if($names[3] != null && $names[3] != ""){
  	  $w = $names[3];
  	}
  	if($names[4] != null && $names[4] != ""){
  	  $h = $names[4];
  	}
  	
  	foreach($obj as $k){
  	  if(!$firstColumn){
  	  	$latArr  .= ', ';
  	  	$longArr .= ', ';
  	  	$nameArr .= ', ';
  	  }
  	  $latArr  .= $k->$names[0]->value;
  	  if($north < $k->$names[0]->value){
  	  	$north = $k->$names[0]->value;
  	  }
  	  if($south > $k->$names[0]->value){
  	  	$south = $k->$names[0]->value;
  	  }
  	  
  	  $longArr .= $k->$names[1]->value;
  	  if($west > $k->$names[1]->value){
  	  	$west = $k->$names[1]->value;
  	  }
  	  if($east < $k->$names[1]->value){
  	  	$east = $k->$names[1]->value;
  	  }  	  
  	  
  	  $nameArr .= $k->$names[2]->value;
  	  $firstColumn = false;
  	}
  	
  	$pre = "<div id='map_canvas_".$randId."' style='width: ".$w."px; height: ".$h."px'></div><script type='text/javascript'
  	src='http://maps.googleapis.com/maps/api/js?sensor=false'></script>
    <script type='text/javascript'>
    function initialize() {
	  var myOptions = {
	  zoom: 4,
	  center: new google.maps.LatLng(0, 0),
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	  };
	  
	  var map = new google.maps.Map(document.getElementById('map_canvas_".$randId."'),
	  myOptions);
	  
	  var southWest = new google.maps.LatLng(".$south.", ".$west.");
	  var northEast = new google.maps.LatLng(".$north.", ".$east.");
	  
	  var bounds = new google.maps.LatLngBounds(southWest, northEast);
	  map.fitBounds(bounds);
	  
	  var lngSpan = northEast.lng() - southWest.lng();
	  var latSpan = northEast.lat() - southWest.lat();
	  
	  var latArray = [".$latArr."];
	  var lonArray = [".$longArr."];
	  var labelArray = [".$nameArr."];
	  
	  for (var i = 0; i < latArray.length; i++) {
	  var position = new google.maps.LatLng(latArray[i], lonArray[i]);
	  var marker = new google.maps.Marker({
	  position: position,
	  map: map
	  });
	  
	  marker.setTitle(labelArray[i]);
	  var infowindow = new google.maps.InfoWindow({
	  content: labelArray[i]
	  });     
	  google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(marker.get('map'), marker);
	  });
	  }
    }
        
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>";
    return $pre;
  }
}
