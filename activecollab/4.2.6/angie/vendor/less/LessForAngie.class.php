<?php

  /**
   * Less compiler for Angie
   * 
   * @package angie.vendor.less
   */
  final class LessForAngie {
    
    /**
     * Compiler instance
     *
     * @var lessc
     */
    static private $compiler;
  
    /**
     * Compile LESS to CSS
     * 
     * @param string $less
     * @return string
     */
    static function compile($less) {
      if(empty(self::$compiler)) {
        self::$compiler = new lessc();
      } // if

      self::$compiler->registerFunction("color_schemes", function($arguments) {
        return array_key_exists(1, $arguments) ? trim($arguments[1], '"\'') : '';
      });
      
      return self::$compiler->parse($less);
    } // compile
    
  }