<?php

  /**
   * Categories context interface
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  interface ICategoriesContext {
    
    /**
     * Return categories context implementation
     *
     * @return ICategoriesContextImplementation
     */
    function availableCategories();
    
  }

?>