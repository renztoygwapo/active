<?php

  /**
   * Smarty implementation for Angie framework
   * 
   * @package angie.vendors.smarty
   */
  final class SmartyForAngie {
    
    /**
     * Global Smarty instance
     *
     * @var Smarty
     */
    static private $instance;
  
    /**
     * Return main Smarty instance
     * 
     * @return Smarty
     */
    static function &getInstance() {
      if(empty(self::$instance)) {
        self::$instance = new Smarty();
      } // if
      
      return self::$instance;
    } // getInstance
    
  }