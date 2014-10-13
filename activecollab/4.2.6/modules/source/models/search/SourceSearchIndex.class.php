<?php

  /**
   * Index for commits imported by source module
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceSearchIndex extends SearchIndex {
  
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'source';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Commits');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'repository_id' => self::FIELD_NUMERIC, 
        'repository' => self::FIELD_STRING, 
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
        if(Authentication::getLoggedUser() instanceof User) {
          $repositories = SourceRepositories::getIdNameMapByUser(Authentication::getLoggedUser());
          if($repositories) {
            $this->filters = array(
              'repository_id' => new EnumerableSearchFilter($this, 'repository_id', lang('Repository'), $repositories), 
            );
          } // if
        } // if
        
        if($this->filters === false) {
          $this->filter = null;
        } // if
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
      return Router::assemble('source_search_index_admin_build');
    } // getBuildUrl
    
  }