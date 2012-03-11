<?php

class Haanga_Extension_Filter_Urifragment
{
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("URIFragment only needs one parameter");
        }

        return hexec('array_pop', hexec('split', '#', $args[0]));
    }
}
