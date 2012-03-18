<?php

class Haanga_Extension_Filter_Path
{
    static function generator($cmp, $args)
    {
        return hexec('parse_url', $args[0], hconst('PHP_URL_PATH'));
    }
}
