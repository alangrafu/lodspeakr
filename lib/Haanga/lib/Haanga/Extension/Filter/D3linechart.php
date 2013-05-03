<?php

class Haanga_Extension_Filter_D3LineChart{
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

  	$columnsAsSeries = false;
  	$series = array();
  	if($columnsAsSeries){
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
    }else{
  	  foreach($obj as $k){  	
  	    $newSerie = array();
  	    $currentSerie = null;
  	    $newItem = array();
  	    foreach($varList as $v){
  	      $name = $v['name'];
  	      $val = $v['value'];
  	      
  	      if($j==0){
  	        $currentSerie = $k->$name->$val;
  	        if(!isset($data[$currentSerie])){ $data[$currentSerie] = array();}  	        
  	      }elseif($j == 1){
  	        $newItem['key'] = $k->$name->$val;
  	      }elseif($j == sizeof($varList)-1){
  	        $newItem['uri'] =  $k->$name->$val;
  	        $data[$currentSerie][] = $newItem;
  	        $newItem = array();
  	      }else{
  	        $newItem['values'] = floatval($k->$name->$val);
  	      }
  	      $j++;
  	    }
  	    $i++;
  	    $j=0;
  	    // 	  	array_push($data, $newItem);
  	  }
  	/*  $data = array(
  	                 array('key' => 'zxc', 'values' => array( 1, 2)),
  	                 array('key' => 'asd', 'values' => array(10, 2)),
  	                 array('key' => 'zxc1', 'values' => array( 11, 2)),
  	                 array('key' => 'asd1', 'values' => array(21, 2)),
  	                 array('key' => 'zxc2', 'values' => array( 23, 2)),
  	                 array('key' => 'asd3', 'values' => array(20, 2)),
  	               );*/
    }
  	
  	//Getting options
  	$options['height'] = 300;
  	$options['width'] = 1000;
  	$options['padding'] = 20;
  	$options['barsProportion'] = 0.8;
  	$options['legendSpace'] = 15;
  	$options['intermediateLines'] = 4;
  	$options['numberOfBars'] = 0;
  	$options['chartProportion'] = .8;
    for($z=$fieldCounter; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	$divId = uniqid("d3linechart_div");
  	$pre = "<div id='".$divId."'></div>
  	<script src='http://d3js.org/d3.v2.min.js?2.9.3'></script>
    <script type='text/javascript'>
    //Adding namespaces
    d3.ns.prefix['vsr'] = 'http://purl.org/twc/vocab/vsr#'; 
    d3.ns.prefix['rdf'] = 'http://www.w3.org/2000/01/rdf-schema#'; 
    d3.ns.prefix['grddl'] = 'http://www.w3.org/2003/g/data-view#';

    var options_$divId = ".json_encode($options)."; 
    var dataset_$divId = ".json_encode($data).";
    var color = d3.scale.category10();
    var stroke = function(d){
      s = ['', '5,5', '5,10', '10,5'];
      return s[d%s.length];
    }
    
    var maxValue_$divId = getMax(dataset_".$divId.");
    var svg = d3.selectAll('#".$divId."')
                .append('svg')
                .attr('width', options_$divId.width)
                .attr('height', options_$divId.height)
                .attr('xmlns:xmlns:vsr','http://purl.org/twc/vocab/vsr#')
                .attr('xmlns:xmlns:grddl', 'http://www.w3.org/2003/g/data-view#')
                .attr('xmlns:xmlns:rdf','http://www.w3.org/2000/01/rdf-schema#');
                
                svg.append('svg:metadata').attr('grddl:grddl:transformation', 'https://raw.github.com/timrdf/vsr/master/src/xsl/grddl/svg.xsl');

//Tooltip
tooltip_$divId = svg.append('text').style('opacity', 0).style('font-family', 'sans-serif').style('font-size', '11px').style('stroke-width', '.5');

var maxHeight_$divId = options_$divId.chartProportion*options_$divId.height;
    var labels_$divId = getLabels(dataset_$divId);
    options_$divId.numberOfBars = getNumberOfBars(dataset_$divId);

    function getMax(d){
     maxValue = 0;
     for(var i in d){
       e = d[i];
       for(var j in e){
         if(maxValue < parseInt(e[j].values)){
           maxValue = parseInt(e[j].values);
         }
       }
     }
     return maxValue+1;
   }  
   
   function getNumberOfBars(d){
     numberOfBars = 0;
     for(var i in d){
       e = d[i];
       aux = 0;
       for(var j in e){
         aux++;
       }
       if(aux > numberOfBars){
         numberOfBars = aux;
       }
     }
     return numberOfBars;
   } 

   function getLabels(d){
     labels = [];
     for(i in d){
       e = d[i];
       for(j in e){
         labels.push(e[j].key);
       }
     return labels
     }
   }
 //Axis
  var xaxis = svg.append('g');
    xaxis.append('line').style('stroke', 'black').style('stroke-width', '2px').attr('x1',  1+options_$divId.legendSpace).attr('y1', maxHeight_$divId).attr('x2', options_$divId.width+options_$divId.padding+ options_$divId.legendSpace).attr('y2', maxHeight_$divId)
    xaxis.selectAll('line.stub')
    var labels_$divId = xaxis.selectAll('text.xaxis')
        .data(labels_".$divId.")
        .enter().append('text').text(function(d){return d})
        .style('font-size', '12px').style('font-family', 'sans-serif')
        .attr('class', 'xaxis')        
        .attr('x', function(d, i){return options_$divId.chartProportion*i*(parseInt(options_$divId.width)/ options_$divId.numberOfBars) + 4*options_$divId.padding + options_$divId.legendSpace})
        .attr('y', function(d, i){return maxHeight_$divId+30;})
        .attr('transform', function(d){return 'translate(-'+(this.getBBox().width/2)+')'});

        
   var yaxis = svg.append('g');
    yaxis.append('line').style('stroke', 'black').style('stroke-width', '2px').attr('x1', 1+options_$divId.padding + options_$divId.legendSpace).attr('y1', maxHeight_$divId).attr('x2', 1+options_$divId.padding + options_$divId.legendSpace).attr('y2', 1)
   for(i=0; i<options_$divId.intermediateLines; i++){
    yaxis.append('line').style('stroke', 'grey').style('stroke-width', '1px').attr('x1', 1 + options_$divId.legendSpace).attr('y1', maxHeight_$divId*(i/options_$divId.intermediateLines)+1).attr('x2', options_$divId.width).attr('y2', maxHeight_$divId*(i/options_$divId.intermediateLines))
   } 

    
//Values
var line = d3.svg.line()
    .x(function(d, i){return options_$divId.chartProportion*i*(parseInt(options_$divId.width) / options_$divId.numberOfBars) + 4*options_$divId.padding + options_$divId.legendSpace})
    .y(function(d, i) {return (1-d.values/maxValue_$divId)*maxHeight_$divId; });

var j=0;
for(var k in dataset_".$divId."){
  svg.append('path').attr('class', 'line_$divId')
        .datum(dataset_".$divId."[k])
        .attr('d', line)
        .style('opacity', 0.8)
        .style('stroke', function(d, i){return color(k)})
        .style('stroke-width', '2px')
        .style('stroke-dasharray', stroke(j))
        .style('fill', 'none')        .append('svg:metadata')
        .append('vsr:vsr:depicts')
        .attr('rdf:rdf:resource', function(d){target = ''; for(var x in d){target += d[x].uri+' ';} return target;});

  svg.selectAll('circle.points_$divId_'+j).data(dataset_".$divId."[k]).enter()
     .append('circle').attr('class', 'points_$divId_'+j)
     .attr('r', 4)
     .attr('cx', function(d, i){return options_$divId.chartProportion*i*(parseInt(options_$divId.width) / options_$divId.numberOfBars) + 4*options_$divId.padding + options_$divId.legendSpace})
     .attr('cy', function(d, i) {return (1-d.values/maxValue_$divId)*maxHeight_$divId; })
     .style('fill', function(d, i){return color(k)}).append('svg:metadata')
     .append('vsr:vsr:depicts')
     .attr('rdf:rdf:resource', function(d){ return d.uri});
  j++
}

        d3.selectAll('rect.bar')
        .on('mouseover', function(){
        d3.select(this).style('opacity', 1); 
        }).on('mouseout', function(){
        d3.select(this).style('opacity', 0.8); 
        });
//Scale        
   for(i=0; i<options_$divId.intermediateLines; i++){
    yaxis.append('text')
         .attr('x', 1)
         .attr('y', maxHeight_$divId*(i/options_$divId.intermediateLines)+1)
         .attr('font-family', 'sans-serif')
         .attr('font-size', '10px')
         .text(maxValue_$divId*(1-i/options_$divId.intermediateLines))
         .attr('transform', 'translate(0,10)');
   } 

//Events
svg.selectAll('circle').on('mouseover', function(e){
        tooltipColor = 'black';
        newX =  parseFloat(d3.select(this).attr('cx')) - 10;
        newY =  parseFloat(d3.select(this).attr('cy')) - 5;
        if(newY > maxHeight_$divId){
          newY -=10;
        }
        if(newY < 10){
          newY +=21;
        }

        tooltip_$divId.style('opacity', 1).style('fill', tooltipColor).attr('y', newY).attr('x', newX).text(e.values);
        d3.select(this).style('opacity', 1); 
        }).on('mouseout', function(){
        d3.select(this).style('opacity', 0.8); 
        tooltip_$divId.style('opacity', 0);
        });
    </script>
    ";
    
    return $pre;
  }
}
