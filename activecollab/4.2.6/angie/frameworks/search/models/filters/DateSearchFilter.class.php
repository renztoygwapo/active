<?php

  /**
   * Filter used to filter date fields
   * 
   * @package angie.framework.search
   * @subpackage models
   */
  class DateSearchFilter extends SearchFilter {
  
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