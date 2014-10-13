<?php

  /**
   * Documents search index
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentsSearchIndex extends SearchIndex {
  
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'documents';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Documents');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'category_id' => self::FIELD_NUMERIC, 
        'category' => self::FIELD_STRING, 
        'name' => self::FIELD_STRING, 
        'body' => self::FIELD_TEXT, 
      );
    } // getFields
    
    
    /**
     * Cached filter definitions for this search index
     *
     * @var array
     */
    private $filters = false;
    
    /**
     * Return filters that can be used to limit results from this search index
     * 
     * @return array
     */
    function getFilters() {
      if($this->filters === false) {
        $this->filters = array( 
          'category_id' => new EnumerableSearchFilter($this, 'category_id', lang('Category'), Categories::getIdNameMap(null, 'DocumentCategory')), 
        );
      } // if
      
      return $this->filters;
    } // getFilters
    
    // ---------------------------------------------------
    //  Rebuild
    // ---------------------------------------------------
    
    /**
     * Return steps to rebuild this search index
     */
    function getRebuildSteps() {
      $steps = parent::getRebuildSteps();
      
      $steps[] = array(
        'text' => lang('Build Index'), 
       	'url' => $this->getBuildUrl(),
      );
      
      return $steps;
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return build index URL
     * 
     * @return string
     */
    function getBuildUrl() {
      return Router::assemble('documents_search_index_admin_build');
    } // getBuildUrl
    
  }