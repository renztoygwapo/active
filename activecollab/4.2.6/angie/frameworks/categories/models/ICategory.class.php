<?php

  /**
   * Category interface
   * 
   * @package angie.frameworks.categories
   * @subpackage models
   */
  interface ICategory {
    
    /**
     * Return ICategory implementation instance for parent object
     *
     * @return ICategoryImplementation
     */
    function category();
    
  }