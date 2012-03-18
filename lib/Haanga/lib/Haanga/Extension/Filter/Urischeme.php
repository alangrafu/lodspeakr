<?php

class Haanga_Extension_Filter_Urischeme
{
    static function generator($cmp, $args)
    {
        return hexec('parse_url', $args[0], hconst('PHP_URL_SCHEME'));
    }
}
