<?php

class Haanga_Extension_Filter_Alert
{
public $is_safe = TRUE;
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("alert filter only needs one parameter");
        }
        $x = $args[0];
        $pre = '<script type="text/javascript">alert("';
        $post = '");</script>';
        return hexec('html_entity_decode', 
          hexec('preg_replace', '/$/', $post, 
          	hexec('preg_replace', '/^/', $pre, $x)));
    }
}
