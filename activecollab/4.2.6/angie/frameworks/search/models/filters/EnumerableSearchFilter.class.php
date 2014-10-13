<?php

  /**
   * Serched filter used for fields with enumerable data
   * 
   * @package angie.framework.search
   * @subpackage models
   */
  class EnumerableSearchFilter extends SearchFilter {
  
    /**
     * Array of possible values
     *
     * @var array
     */
    protected $possibilities;
    
    /**
     * Construct search filter index
     * 
     * @param SearchIndex $index
     * @param string $field
     * @param string $label
     * @param array $possibilities
     */
    function __construct(SearchIndex $index, $field, $label, $possibilities) {
      parent::__construct($index, $field, $label);
      
      $this->possibilities = $possibilities;
    } // __construct
    
    /**
     * Render search filter controls for advanced filtering
     * 
     * @param string $id
     * @param string $name
     */
    function renderControls($id, $name) {
      return HTML::selectFromPossibilities($name . '[criterion]', array(
        SearchCriterion::IS => 'is', 
        SearchCriterion::IS_NOT => 'is not' 
      )) . ' ' . HTML::optionalSelectFromPossibilities($name . '[value]', $this->possibilities, null, null, lang('Any'));
    } // renderControls
    
  }