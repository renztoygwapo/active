<?php

  /**
   * Access log interface
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IAccessLog {
  
    /**
     * Return access log helper instance
     * 
     * @return IAccessLogImplementation
     */
    function accessLog();
    
  }