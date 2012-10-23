<?php

class Haanga_Extension_Filter_D3ParallelCoordinates{
  public $is_safe = TRUE;
  static function main($obj, $varname){

  	$nodesArr = array();
  	$n=0;
  	$first="";
  	$nodes = array();
  	$links = array();
  	$names = explode(",", $varname);
  	$varList = array();
  	$randId = uniqid("_ID_");

  	foreach($names as $v){
  	  if(strpos($v,"=")){
  	    break;
  	  }
  	  $variable['name'] = $v;
  	  $variable['value'] = 'value';
  	  if(strpos($v, ".")){
  	    $aux = explode(".", $v);
  	    $variable['name'] = $aux[0];
  	    $variable['value'] = $aux[1];
  	  }
  	  $fieldCounter++;
  	  $columnType = 'number';
  	  if($firstColumn){
  	  	$columnType = 'string';
  	  	$firstColumn = false;
  	  }
  	  array_push($varList, $variable);
  	  //$data .= "        data.addColumn('".$columnType."', '".$variable['name']."');\n";
  	}
  	//options
  	$options = array();
  	$options['width'] = 960;
  	$options['height'] = 500;
  	$options['color'] = '#aec7e8';
  	$options['highlightedColor'] = '#00477f';
  	$options['radius'] = 10;
  	$options['highlightedStrokeWidth'] = '3px';
  	$options['strokeWidth'] = '1px';
  	for($z=2; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	$rows = array();
  	foreach($obj as $k){
  	  $row = array();
  	  foreach($varList as $v){
  	    $variable = $v['name'];
  	    $val = $v['value'];
  	    $row[$variable] = $k->$variable->$val;
  	  }
  	  array_push($rows, $row);
  	}  	

  	$json = $rows;
  	
  	
  	$pre = '<div id="'.$randId.'"><div id="name'.$randId.'" style="font-family:sans-serif;font-size:15px;height:25px"><h2> </h2></div></div><script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script>
/* Based on http://bl.ocks.org/1341021 */
function initD3ParallelCoordinates'.$randId.'(json){
var width = '.$options['width'].',
    height = '.$options['height'].'

var m = [30, 10, 10, 10],
    w = '.$options['width'].' - m[1] - m[3],
    h = '.$options['height'].' - m[0] - m[2];
var line = d3.svg.line(),
    axis = d3.svg.axis().orient("left"),
    background,
    foreground;

var svg = d3.select("#'.$randId.'").append("svg")
    .attr("width", width)
    .attr("height", height).style("font", "10px sans-serif")
    .append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");
;
    
var x = d3.scale.ordinal().rangePoints([0, w], 1),
    y = {};

  // Extract the list of dimensions and create a scale for each.
  x.domain(dimensions = d3.keys(json[0]).filter(function(d) {
    return d != "'.$varList[0]['name'].'" && (y[d] = d3.scale.linear()
        .domain(d3.extent(json, function(p) { return +p[d]; }))
        .range([h, 0]));
  }));

  // Add grey background lines for context.
  background = svg.append("g")
      .attr("class", "background")
    .selectAll("path")
      .data(json)
    .enter().append("path").style("fill", "none").style("stroke", "#ccc").style("stroke-opacity", .4).style("shape-rendering", "crispEdges")
      .attr("d", path);

  // Add blue foreground lines for focus.
  foreground = svg.append("g")
      .attr("class", "foreground")
    .selectAll("path")
      .data(json)
    .enter().append("path").style("fill", "none").style("stroke-width", "'.$options['strokeWidth'].'").style("stroke", "'.$options['color'].'").style("stroke-opacity", .7)
      .attr("d", path).attr("name", function(d){console.log("Adding "+d.'.$varList[0]['name'].');return d.'.$varList[0]['name'].'}).on("mouseover", mouseover).on("mouseout", mouseout);

  // Add a group element for each dimension.
  var g = svg.selectAll(".dimension")
      .data(dimensions)
    .enter().append("g")
      .attr("class", "dimension")
      .attr("transform", function(d) { return "translate(" + x(d) + ")"; });

  // Add an axis and title.
  g.append("g")
      .attr("class", "axis")
      .each(function(d) { d3.select(this).call(axis.scale(y[d])); })
    .append("text")
      .attr("text-anchor", "middle")
      .attr("y", -9)
      .text(String);
  svg.selectAll(".axis line, .axis path").style("fill", "none").style("stroke", "#000")
  // Add and store a brush for each axis.
  g.append("g")
      .attr("class", "brush").style("fill-opacity",.3).style("stroke", "#fff").style("shape-rendering", "crispEdges")
      .each(function(d) { d3.select(this).call(y[d].brush = d3.svg.brush().y(y[d]).on("brush", brush)); })
    .selectAll("rect")
      .attr("x", -8)
      .attr("width", 16);

// Returns the path for a given data point.
function path(d) {
  return line(dimensions.map(function(p) { return [x(p), y[p](d[p])]; }));
}

// Handles a brush event, toggling the display of foreground lines.
function brush() {
  var actives = dimensions.filter(function(p) { return !y[p].brush.empty(); }),
      extents = actives.map(function(p) { return y[p].brush.extent(); });
  foreground.style("display", function(d) {
    return actives.every(function(p, i) {
      return extents[i][0] <= d[p] && d[p] <= extents[i][1];
    }) ? null : "none";
  });
}

function mouseover(){
  d3.select(this).style("stroke-width", "'.$options['highlightedStrokeWidth'].'").style("stroke", "'.$options['highlightedColor'].'");
  d3.select("#name'.$randId.'").html("<h2>"+d3.select(this).attr("name")+"</h2>");
}
function mouseout(){
  d3.select(this).style("stroke-width", "'.$options['strokeWidth'].'").style("stroke", "'.$options['color'].'");
  d3.select("#name'.$randId.'").html("<h2></h2>");
}
}
    
var jsonD3'.$randId.' = '.json_encode($json).';
initD3ParallelCoordinates'.$randId.'(jsonD3'.$randId.')
</script>';
  	return $pre.$post;
  }
}
