<?php

  // Build on top of search index controller
  AngieApplication::useController('search_index_admin', SEARCH_FRAMEWORK_INJECT_INTO);

  /**
   * Names search index administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class NamesSearchIndexAdminController extends SearchIndexAdminController {
    
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof NamesSearchIndex)) {
        $this->response->operationFailed();
      } // if
      
      if(!$this->request->isAsyncCall() || !$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * Index company names
     */
    function companies() {
      try {
        $companies = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE state > ?', STATE_DELETED);
      
        if($companies) {
          foreach($companies as $company) {
            
            Search::set($this->active_search_index, array(
              'class' => 'User', 
              'id' => $company['id'], 
              'context' => "people:companies/$company[id]", 
              'name' => $company['name'],
              'visibility' => VISIBILITY_NORMAL, 
            ));
          } // foreach
        } // if
        
        $this->response->ok();
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // companies
    
    /**
     * Index users
     */
    function users() {
      try {
        $users = DB::execute('SELECT id, company_id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE state > ?', STATE_DELETED);
      
        if($users) {
          foreach($users as $user) {
            $company_id = (integer) $user['company_id'];
            
            Search::set($this->active_search_index, array(
              'class' => 'User', 
              'id' => $user['id'], 
              'context' => "people:companies/$company_id/users/$user[id]", 
              'name' => Users::getUserDisplayName($user), 
              'short_name' => $user['email'],
              'visibility' => VISIBILITY_NORMAL, 
            ));
          } // foreach
        } // if
        
        $this->response->ok();
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // users
    
    /**
     * Build projects search index
     */
    function projects() {
      try {
        $projects = DB::execute('SELECT id, name, slug FROM ' . TABLE_PREFIX . 'projects WHERE state > ?', STATE_DELETED);
      
        if($projects) {
          foreach($projects as $project) {
            Search::set($this->active_search_index, array(
              'class' => 'Project', 
              'id' => $project['id'], 
              'context' => "projects:projects/$project[id]", 
              'name' => $project['name'], 
              'short_name' => $project['slug'], 
            	'visibility' => VISIBILITY_NORMAL, 
            ));
          } // foreach
        } // if
        
        $this->response->ok();
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // projects
    
    /**
     * Create names index for a given project
     */
    function project() {
      try {
        $project_id = $this->request->getId('project_id');
        
        $project = $project_id ? Projects::findById($project_id) : null;
        
        if($project instanceof Project) {
          $milestones = DB::execute("SELECT id, type, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Milestone' AND project_id = ? AND state >= ?", $project_id, STATE_ARCHIVED);
          if($milestones) {
            list($comments, $subtasks) = ProjectObjects::getCommentsAndSubtasksForSearch($milestones, true, false, STATE_VISIBLE);

            foreach($milestones as $milestone) {
              $milestone_id = (integer) $milestone['id'];

              Search::set($this->active_search_index, array(
                'class' => 'Milestone', 
                'id' => $milestone_id,
                'context' => "projects:projects/$project_id/milestones/$milestone_id",
                'name' => $milestone['name'], 
                'body' => $milestone['body'] ? $milestone['body'] : null,
                'visibility' => VISIBILITY_NORMAL,
                'comments' => isset($comments['Milestone']) && $comments['Milestone'][$milestone_id] ? $comments['Milestone'][$milestone_id] : '',
              ));
            } // foreach
          } // if
          
          EventsManager::trigger('on_build_names_search_index_for_project', array(&$this->active_search_index, &$project));
          
          $this->response->ok();
        } else {
          $this->response->notFound();
        } // if
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } // project
    
  }