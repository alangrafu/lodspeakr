<?php

class Haanga_Extension_Filter_Date
{
    static function generator($compiler, $args)
    {
//        return hexec( 'date', $args[1], hexec('strtotime', $args[1]));
        return hexec('date', $args[1], hexec('strtotime', $args[0]));
    }
}
    

