<?php

  /**
   * Search items criteria
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  abstract class SearchItemsCriteria {
  
    /**
     * List of individual criterions
     * 
     * @var array
     */
    protected $criterions = array();
    
    // ---------------------------------------------------
    //  Criterions
    // ---------------------------------------------------
    
    /**
     * Return list of criterions
     * 
     * @return array
     */
    function getCriterions() {
      return $this->criterions;
    } // getCriterions
    
    /**
     * Add new criterion
     * 
     * @param SearchCriterion $criterion
     */
    function addCriterion(SearchCriterion $criterion) {
      $this->criterions[] = $criterion;
    } // addCriterion
    
  }