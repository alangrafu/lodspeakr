<?php

class Haanga_Extension_Filter_Pop
{
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("Pop only needs two parameter");
        }

        return hexec('array_pop', $args[0]);
    }
}
