<?php

  /**
   * Search item interface
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  interface ISearchItem {
  
    /**
     * Return search helper instance
     * 
     * @return ISearchItemImplementation
     */
    function &search();
    
  }