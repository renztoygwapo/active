<?php

  /**
   * Search criterion definition
   * 
   * @package angie.frameworks.search
   * @subpackage models
   */
  class SearchCriterion {
    
    // Supported criterions
    const IS = '=';
    const IS_NOT = '!=';
    const GREATER = '>';
    const GREATER_OR_EQUAL = '>=';
    const SMALLER = '<';
    const SMALLER_OR_EQUAL = '<=';
    const LIKE = 'like';
    
    // Criterion type
    const FILTER_RESULT = 'filter';
    const EXTEND_RESULT  = 'extend';
    
    /**
     * Field name
     *
     * @var string
     */
    private $field;
    
    /**
     * Crterion
     *
     * @var string
     */
    private $criterion;
    
    /**
     * Value that needs to be matched
     *
     * @var mixed
     */
    private $value;
    
    /**
     * How this criterion influences search result
     *
     * @var string
     */
    private $type;
  
    /**
     * Construct new search criterion
     * 
     * @param string $field
     * @param string $criterion
     * @param mixed $value
     * @param string $type
     */
    function __construct($field, $criterion, $value, $type = SearchCriterion::FILTER_RESULT) {
      $this->field = $field;
      $this->criterion = $criterion;
      $this->value = $value;
      $this->type = $type;
    } // __construct
    
    /**
     * Return field that will be used
     * 
     * @return string
     */
    function getField() {
      return $this->field;
    } // getField
    
    /**
     * Return criterion that will be used
     * 
     * @return string
     */
    function getCriterion() {
      return $this->criterion;
    } // getCriterion
    
    /**
     * Return value that needs to be matched
     * 
     * @return mixed
     */
    function getValue() {
      return $this->value;
    } // getValue
    
    /**
     * Return criterion type
     * 
     * @return string
     */
    function getType() {
      return $this->type;
    } // getType
    
  }