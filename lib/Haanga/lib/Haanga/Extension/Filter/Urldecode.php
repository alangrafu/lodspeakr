<?php

class Haanga_Extension_Filter_UrlDecode
{

    public static function generator($cmp, $args)
    {
        $cmp->var_is_safe = TRUE;
        return hexec('urldecode', $args[0]);
    }
}
