<?php

class Haanga_Extension_Filter_LodspkTextInput{
  public $is_safe = TRUE;
  static function main($obj, $varname){
        $pre = "<input data-predicate='$obj' class='lodspk-form-input'/>";
    return $pre;
  }
}
