<?php

class Haanga_Extension_Filter_D3StackedColumnChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = array();
  	$i = 0;
    $options = array();
  	$randId = rand();
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$j = 0;

  	
  	
  	$fieldCounter=0;
  	$varList = array();
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
  	  array_push($varList, $variable);
  	}

  	$series = array();
  	foreach($obj as $k){  	
  	  $newItem = array();
  	  foreach($varList as $v){
  	    $name = $v['name'];
  	    $val = $v['value'];

  	  	if($j==0){
  	  	  //$newItem[$j]['x'] = $value;
  	  	}else{
  	  	  $series[$name]['key'] = $name;
  	  	  $series[$name]['values'][] = $k->$name->$val;
  	  	}
  	  	$j++;
  	  } 
  	  $i++;
  	  $j=0;
// 	  	array_push($data, $newItem);
  	}
    foreach($series as $v){
      $data[] = $v;
    }
  	
  	//Getting options
  	$options['height'] = 300;
  	$options['width'] = 1000;
  	$options['padding'] = 20;
  	$options['barsProportion'] = 0.8;
  	$options['legendSpace'] = 15;
  	$options['intermediateLines'] = 4;
    for($z=$fieldCounter; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	$divId = uniqid("columnchart_div");
  	$pre = "<div id='".$divId."'>
  	</div>
  	<script src='http://d3js.org/d3.v2.min.js?2.9.3'></script>
    <script type='text/javascript'>
    //Adding namespaces
    d3.ns.prefix['vsr'] = 'http://purl.org/twc/vocab/vsr#'; 
    d3.ns.prefix['rdf'] = 'http://www.w3.org/2000/01/rdf-schema#'; 

    var options_$divId = ".json_encode($options)."; 
    var dataset_$divId = ".json_encode($data).";
    var color = d3.scale.category10();
    
    var maxValue_$divId = getMax(dataset_$divId);
    var svg = d3.selectAll('#".$divId."')
                .append('svg')
                .attr('width', options_$divId.width)
                .attr('height', options_$divId.height)
                .attr('xmlns:xmlns:vsr','http://purl.org/twc/vocab/vsr#')
                .attr('xmlns:xmlns:rdf','http://www.w3.org/2000/01/rdf-schema#');
    var maxHeight_$divId = options_$divId.barsProportion*options_$divId.height;


   function getMax(d){
     maxValues = [];
     for(var i in d){
       e = d[i];
       for(var j in e.values){
         if(maxValues[e.key] == undefined){
          maxValues[e.key] = 0;
         }
         maxValues[e.key] += parseInt(e.values[j]);
       }
     }
     r = 0;
     for(var i in maxValues){
     aux = parseInt(maxValues[i]);
       if(aux > r){
         r = aux;
       }
     }
     return r+1;
   }    
   
//Axis
  var xaxis = svg.append('g');
    xaxis.append('line').style('stroke', 'black').style('stroke-width', '2px').attr('x1',  1+options_$divId.legendSpace).attr('y1', maxHeight_$divId).attr('x2', options_$divId.width+options_$divId.padding+ options_$divId.legendSpace).attr('y2', maxHeight_$divId)
    xaxis.selectAll('line.stub')
    var labels_$divId = xaxis.selectAll('text.xaxis')
        .data(dataset_$divId)
        .enter().append('text').text(function(d){return d.key})
        .style('font-size', '12px').style('font-family', 'sans-serif')
        .attr('class', 'xaxis')        
        .attr('x', function(d, i){return (options_$divId.width / dataset_$divId.length - 4*options_$divId.padding)/2+options_$divId.barsProportion *i* (parseInt(options_$divId.width) / dataset_$divId.length) + 4*options_$divId.padding + options_$divId.legendSpace})
        .attr('y', function(d, i){return maxHeight_$divId+30;})
        .attr('transform', function(d){return 'translate(-'+this.getBBox().width/2+')'});

        
   var yaxis = svg.append('g');
    yaxis.append('line').style('stroke', 'black').style('stroke-width', '2px').attr('x1', 1+options_$divId.padding + options_$divId.legendSpace).attr('y1', maxHeight_$divId).attr('x2', 1+options_$divId.padding + options_$divId.legendSpace).attr('y2', 1)
   for(i=0; i<options_$divId.intermediateLines; i++){
    yaxis.append('line').style('stroke', 'grey').style('stroke-width', '1px').attr('x1', 1 + options_$divId.legendSpace).attr('y1', maxHeight_$divId*(i/options_$divId.intermediateLines)+1).attr('x2', options_$divId.width).attr('y2', maxHeight_$divId*(i/options_$divId.intermediateLines))
   } 

   
//Values
baseline = [];
for(var k in dataset_$divId){
key = dataset_".$divId."[k].key;
  baseline[key] = maxHeight_$divId;
  svg.selectAll('d.series')
     .data(dataset_".$divId."[k].values).enter()
     .append('rect').attr('class', 'bar')
        .attr('x', function(d, i) {
			   		return options_$divId.barsProportion *k* (parseInt(options_$divId.width) / dataset_$divId.length) + 4*options_$divId.padding + options_$divId.legendSpace;
			   })
			   .attr('y', function(d, i){
			   r = maxHeight_$divId*(1-parseInt(d)/maxValue_$divId) - (maxHeight_$divId-baseline[key]);			   
			   baseline[key] = r;
			   return r;

			   })
			   .attr('width', options_$divId.width / dataset_$divId.length - 4*options_$divId.padding)
			   .attr('height', function(d){ 
			   return maxHeight_$divId*d/maxValue_$divId
			   })
        .style('opacity', 0.8).style('fill', function(d, i){return color(i)})
        .append('svg:metadata')
        .append('vsr:vsr:depicts')
        .attr('rdf:rdf:resource', function(d){return dataset_".$divId."[k].values;});
}

 
//Tooltip
tooltip_$divId = svg.append('text').style('opacity', 0).style('font-family', 'sans-serif').style('font-size', '12px').style('fill', 'white').style('stroke-width', '.5');

//Events
d3.selectAll('rect.bar')
        .on('mouseover', function(e){
        newX =  parseFloat(d3.select(this).attr('x')) + .5*parseFloat(d3.select(this).attr('width'));
        newY =  parseFloat(d3.select(this).attr('y'));
        tooltip_$divId.style('opacity', 1).attr('y', newY+10).attr('x', newX).text(e);
        d3.select(this).style('opacity', 1); 
        }).on('mouseout', function(){
        d3.select(this).style('opacity', 0.8); 
        });
        
   for(i=0; i<options_$divId.intermediateLines; i++){
    yaxis.append('text').attr('x', 1).attr('y', maxHeight_$divId*(i/options_$divId.intermediateLines)+1).attr('font-family', 'sans-serif').attr('font-size', '10px').text(maxValue_$divId*(1-i/options_$divId.intermediateLines)).attr('transform', 'translate(0,10)');
   } 
    </script>
    ";
    return $pre;
  }
}
