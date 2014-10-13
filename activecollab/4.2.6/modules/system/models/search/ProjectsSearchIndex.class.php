<?php

  /**
   * Project search index definition
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectsSearchIndex extends SearchIndex {

    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'projects';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Projects');
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
        'company_id' => self::FIELD_NUMERIC,
      	'company' => self::FIELD_STRING,  
        'name' => self::FIELD_STRING,
        'slug' => self::FIELD_STRING, 
        'overview' => self::FIELD_TEXT,
        'completed_on' => self::FIELD_DATETIME, 
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
        $visible_company_ids = Authentication::getLoggedUser() instanceof User ? Authentication::getLoggedUser()->visibleCompanyIds() : null;
        
        $this->filters = array(
          'company_id' => new EnumerableSearchFilter($this, 'company_id', lang('Client'), Companies::getIdNameMap($visible_company_ids)), 
          'category_id' => new EnumerableSearchFilter($this, 'category_id', lang('Category'), Categories::getIdNameMap(null, 'ProjectCategory')), 
        );
      } // if
      
      return $this->filters;
    } // getFilters
    
    /**
     * Return context filter for a given user
     * 
     * @param IUser $user
     * @return string
     */
    function getUserFilter(IUser $user) {
      if($user->isProjectManager()) {
        return null;
      } else {
        $project_ids = Projects::findIdsByUser($user);
        
        if($project_ids) {
          return new SearchCriterion('item_id', SearchCriterion::IS, $project_ids);
        } else {
          return false;
        } // if
      } // if
    } // getUserFilter
    
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
      return Router::assemble('projects_search_index_admin_build');
    } // getBuildUrl
    
  }