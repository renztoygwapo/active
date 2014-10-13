<?php

  // Build on top of search index controller
  AngieApplication::useController('search_index_admin', SEARCH_FRAMEWORK_INJECT_INTO);

  /**
   * Project objects search index administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectObjectsSearchIndexAdminController extends SearchIndexAdminController {
  
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof ProjectObjectsSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $project_id = $this->request->getId('project_id');
        
        $project = $project_id ? Projects::findById($project_id) : null;
        
        if($project instanceof Project) {
          ProjectObjects::rebuildProjectSearchIndex($project, $this->active_search_index);

          $this->response->ok();
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // build
    
  }