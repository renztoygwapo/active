<?php

  /**
   * Index for quick search
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class NamesSearchIndex extends SearchIndex {
  
    /**
     * Return short name of this index
     * 
     * @return string
     */
    function getShortName() {
      return 'names';
    } // getShortName
  
    /**
     * Return index name
     * 
     * @return string
     */
    function getName() {
      return lang('Quick Search (Names)');
    } // getName
    
    /**
     * Return index fields
     * 
     * @return array
     */
    function getFields() {
      return array(
        'name' => self::FIELD_STRING,
      	'short_name' => self::FIELD_STRING,
        'body' => self::FIELD_TEXT,
        'comments' => self::FIELD_TEXT,
        'subtasks' => self::FIELD_TEXT,
        'visibility' => self::FIELD_NUMERIC
      );
    } // getFields

    /**
     * Return minimal state of objects that are added to this index
     *
     * @return int
     */
    function getMinState() {
      return STATE_VISIBLE;
    } // getMinState
    
    /**
     * Return context filter for a given user
     * 
     * @param IUser $user
     * @return string
     */
    function getUserFilter(IUser $user) {
      if($user instanceof User) {
        list($contexts, $ignore_contexts) = ApplicationObjects::getVisibileContexts($user, null, array('projects', 'people', 'documents'));
        
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
    
    /**
     * Return true if this index is considered advanced search
     * 
     * @return boolean
     */
    function isAdvanced() {
      return false;
    } // isAdvanced
    
    // ---------------------------------------------------
    //  Rebuild
    // ---------------------------------------------------
    
    /**
     * Return steps to rebuild this search index
     */
    function getRebuildSteps() {
      $steps = parent::getRebuildSteps();
      
      $steps[] = array(
        'text' => lang('Build Companies Index'), 
       	'url' => $this->getBuildUrl('companies'),
      );
      
      $steps[] = array(
        'text' => lang('Build Users Index'), 
       	'url' => $this->getBuildUrl('users'),
      );
      
      $steps[] = array(
        'text' => lang('Build Projects Index'), 
       	'url' => $this->getBuildUrl('projects'),
      );
      
      $projects = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'projects WHERE state >= ?', STATE_VISIBLE);
      
      if($projects) {
        foreach($projects as $project) {
          $steps[] = array(
            'text' => lang('Build Index for ":name" Project', array('name' => $project['name'])), 
           	'url' => $this->getBuildUrl('project', array('project_id' => $project['id'])),
          );
        } // foreach
      } // if
      
      EventsManager::trigger('on_rebuild_names_search_index_steps', array(&$steps));
      
      return $steps;
    } // getRebuildSteps
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return build index URL
     * 
     * @param string
     * @param null|array $params
     * @return string
     */
    function getBuildUrl($action, $params = null) {
      if(is_array($params)) {
        $params['action'] = $action;
      } else {
        $params = array('action' => $action);
      } // if
      
      return Router::assemble('names_search_index_admin_build', $params);
    } // getBuildUrl
    
  }