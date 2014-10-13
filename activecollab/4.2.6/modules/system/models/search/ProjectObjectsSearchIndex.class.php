<?php

  /**
   * Project objects search index
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectsSearchIndex extends SearchIndex {
  
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'project_objects';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Project Objects');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'project_id' => self::FIELD_NUMERIC, 
        'project' => self::FIELD_STRING, 
        'milestone_id' => self::FIELD_NUMERIC, 
        'milestone' => self::FIELD_STRING, 
        'category_id' => self::FIELD_NUMERIC, 
        'category' => self::FIELD_STRING,
        'visibility' => self::FIELD_NUMERIC, 
        'name' => self::FIELD_STRING, 
        'body' => self::FIELD_TEXT,
        'assignee_id' => self::FIELD_NUMERIC, 
        'assignee' => self::FIELD_STRING, 
        'priority' => self::FIELD_NUMERIC, 
        'due_on' => self::FIELD_DATE, 
        'completed_on' => self::FIELD_DATETIME,
        'comments' => self::FIELD_TEXT,
        'subtasks' => self::FIELD_TEXT,
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
          'project_id' => new EnumerableSearchFilter($this, 'project_id', lang('Project'), Projects::getIdNameMap(Authentication::getLoggedUser())),
          //'milestone_id' => new EnumerableSearchFilter($this, 'project_id', lang('Milestone'), Projects::getIdNameMap(Authentication::getLoggedUser())), 
          //'category_id' => new EnumerableSearchFilter($this, 'category_id', lang('Category'), Projects::getIdNameMap(Authentication::getLoggedUser())), 
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
      if($user instanceof User) {
        list($contexts, $ignore_contexts) = ApplicationObjects::getVisibileContexts($user, null, array('projects'));
        
        if(is_foreachable($contexts)) {
          $conditions = array(
            'context_conditions' => new IncludeSearchItemsCriteria(), // Second level, because only second level criterions are OR-ed
          );
          
          foreach($contexts as $context) {
            $conditions['context_conditions']->addCriterion(new SearchCriterion('item_context', SearchCriterion::LIKE, $context, SearchCriterion::EXTEND_RESULT));
          } // foreach
          
          if(is_foreachable($ignore_contexts)) {
            $conditions['ignore_context_conditions'] = new ExcludeSearchItemsCriteria();
            
            foreach($ignore_contexts as $context) {
              $conditions['ignore_context_conditions']->addCriterion(new SearchCriterion('item_context', SearchCriterion::LIKE, $context, SearchCriterion::EXTEND_RESULT));
            } // foreach
          } // if
          
          return $conditions;
        } else {
          return false; // Nothing that this user can see
        } // if
      } else {
        return false; // No names search for anonymous users
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
      
      $projects = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'projects WHERE state >= ?', STATE_ARCHIVED);
      
      if($projects) {
        foreach($projects as $project) {
          $steps[] = array(
            'text' => lang('Build Index for ":name" Project', array('name' => $project['name'])), 
           	'url' => $this->getBuildUrl($project['id']),
          );
        } // foreach
      } // if
      
      return $steps;
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return build index URL
     * 
     * @param integer $project_id
     * @return string
     */
    function getBuildUrl($project_id) {
      return Router::assemble('project_objects_search_index_admin_build', array('project_id' => $project_id));
    } // getBuildUrl
    
  }