<?php

  /**
   * Description of search filter used in advanced search
   * 
   * @package angie.framework.search
   * @subpackage models
   */
  abstract class SearchFilter {
    
    /**
     * Parent index instance
     *
     * @var SearchIndex
     */
    protected $index;
    
    /**
     * Field name
     *
     * @var string
     */
    protected $field;
  
    /**
     * Filter label
     *
     * @var string
     */
    protected $label;
    
    /**
     * Construct search filter index
     * 
     * @param SearchIndex $index
     */
    function __construct(SearchIndex $index, $field, $label) {
      $this->index = $index;
      $this->field = $field;
      $this->label = $label;
    } // __construct
    
    /**
     * Render filter controls
     * 
     * @return string
     */
    function render() {
      $id = 'backedn_search_filter' . $this->index->getShortName() . '_' . $this->field;
      
      return '<div class="search_filter">' . 
        '<div class="backend_search_filter_label">' . clean($this->label) . '</div>' . 
        '<div class="backend_search_filter_controls">' . $this->renderControls($id, 'search[filters][' . $this->field . ']') . '</div>' . 
      '</div>';
    } // render
    
    /**
     * Render search filter controls for advanced filtering
     * 
     * @param string $id
     * @param string $name
     * @return string
     */
    abstract function renderControls($id, $name);
    
  }