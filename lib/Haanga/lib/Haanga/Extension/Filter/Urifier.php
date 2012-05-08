<?php

class Haanga_Extension_Filter_Urifier
{
  static function main($uri)
  {
    $newUri = preg_replace('/^http:\/\//', 'http/', $uri);
    $newUri = preg_replace('/#/', '__hash__', $newUri);
    $newUri = preg_replace('/\?/', '__qmark__', $newUri);
    return $newUri;
  }
}


