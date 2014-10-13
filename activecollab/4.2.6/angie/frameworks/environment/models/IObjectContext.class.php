<?php

  /**
   * Object context interface that defines required context functions
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IObjectContext {
  
    /**
     * Return object context domain
     * 
     * @return string
     */
    function getObjectContextDomain();
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath();
    
  }