<?php

class Haanga_Extension_Filter_Googlemaps{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$randId = uniqid("_mapID_");
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$north = -90; $south=90; $east=-180; $west = 180;
  	$latArr =""; $longArr=""; $nameArr = "";
  	$options = array();
  	$options['width'] = 500;
  	$options['height'] = 500;
  	for($z=3; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }
  	$w = $options['width'];
  	$h = $options['height'];
        if(isset($options['zoom'])){
  	  $options['zoom'] = intval($options['zoom']);
        }

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
  	$pre = "<div style='width:".$w."px;height:".$h."px;'><div id='map_canvas' style='height:100%;width:100%;border: 1px solid #333;' ></div></div>
    <script src='https://maps.googleapis.com/maps/api/js?sensor=false'></script>
    <script type='text/javascript'>
    //<![CDATA[
    function initialize_$randId() {
	  
	  var southWest = new google.maps.LatLng(".$south.", ".$west.");
	  var northEast = new google.maps.LatLng(".$north.", ".$east.");
	  
	  
	  var lngSpan = southWest.lng() - northEast.lng();
	  var latSpan = southWest.lat() + northEast.lat();
	  var locations = ".json_encode($points).";
	  
	  var mapOptions$randId = ".json_encode($options).";
	  mapOptions$randId.mapTypeId= google.maps.MapTypeId.ROADMAP;
	  
    var map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions$randId); 
	  var bounds = new google.maps.LatLngBounds(southWest, northEast);
    if(mapOptions$randId.zoom){
	    var zoomChangeBoundsListener = google.maps.event.addListener(map, 'bounds_changed', function(event) {
	  google.maps.event.removeListener(zoomChangeBoundsListener);
	  map.setZoom( mapOptions$randId.zoom );
    });
    }
	  map.fitBounds(bounds);

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i].lat, locations[i].long),
        title: locations[i].label,
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent('<p>'+locations[i].label+'</p>')
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
	  
    }
    initialize_$randId();
    //]]>
    </script>";
    return $pre;
  }
}
