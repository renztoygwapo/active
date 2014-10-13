<?php

  /**
   * Inspector interface
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IInspector {
  
    /**
     * Return inspector helper instance
     * 
     * @return IInspectorImplementation
     */
    function inspector();
    
  }