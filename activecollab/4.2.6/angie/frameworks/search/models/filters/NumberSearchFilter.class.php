<?php

  /**
   * Search filter for number columns
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  class NumberSearchFilter extends SearchFilter {
  
    /**
     * Render controls
     * 
     * @param string $id
     * @param string $name
     */
    function renderControls($id, $name) {
      throw new NotImplementedError(__METHOD__);
    } // renderControls
    
  }