<?php

class Haanga_Extension_Filter_gRaphaelBarChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	global $conf;
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$labels = "[";
  	$data   = "[[";
  	$firstLabel = true;

  	foreach($obj as $k){  	
  	  if(!$firstLabel){
  	  	$labels .= ',';
  	  	$data .= ',';
  	  }
  	  $firstLabel = false;
  	  $labels .='"'.$k->$names[0]->value.'"';
  	  $data   .='"'.$k->$names[1]->value.'"';
  	}
  	$labels .= "]";
  	$data   .= "]]";
  	
  	$pre = '<div id="raphaelholder"></div><script src="'.$conf['basedir'].'js/raphael/raphael.js"></script>
        <script src="'.$conf['basedir'].'js/raphael/g.raphael.js"></script>
        <script src="'.$conf['basedir'].'js/raphael/g.bar.js"></script>
  <script type="text/javascript">
    window.onload = function () {
                var r = Raphael("raphaelholder"),
                    fin = function () {
                        this.flag = r.popup(this.bar.x, this.bar.y, this.bar.value || "0").insertBefore(this);
                    },
                    fout = function () {
                        this.flag.animate({opacity: 0}, 300, function () {this.remove();});
                    },
                    fin2 = function () {
                        var y = [], res = [];
                        for (var i = this.bars.length; i--;) {
                            y.push(this.bars[i].y);
                            res.push(this.bars[i].value || "0");
                        }
                        this.flag = r.popup(this.bars[0].x, Math.min.apply(Math, y), res.join(", ")).insertBefore(this);
                    },
                    fout2 = function () {
                        this.flag.animate({opacity: 0}, 300, function () {this.remove();});
                    },
                    txtattr = { font: "12px sans-serif" };
                    var data = '.$data.';
                    var labels = '.$labels.';
                    r.barchart(10, 10, 320, 220, data).hover(fin, fout);
                    console.log(r);//.axis(20, 140, 620, 10, 580, labels.length, 0, labels, "+", 5); 
                    }
    </script>';
    return $pre;
  }
}
