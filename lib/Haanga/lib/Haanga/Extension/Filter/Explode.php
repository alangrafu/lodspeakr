<?php

class Haanga_Extension_Filter_Explode
{
    static function generator($compiler, $args)
    {
        if (count($args) != 2) {
            $compiler->Error("Explode only needs two parameter");
        }

        return hexec('explode', $args[1], $args[0]);
    }
}
