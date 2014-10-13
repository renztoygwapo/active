<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Application level object contexts implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
  
    /**
     * Rebuild people entries
     */
    function rebuild_people() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_people
    
    /**
     * Rebuild project entries
     */
    function rebuild_projects() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating project contexts @ ' . __CLASS__);
          
          $projects = DB::execute('SELECT id FROM ' . TABLE_PREFIX . 'projects WHERE state >= ?', STATE_TRASHED);
          if($projects) {
            $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
            
            foreach($projects as $project) {
              $batch->insert('Project', $project['id'], "projects:projects/$project[id]");
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Project contexts updated @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update project contexts @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_projects
    
    /**
     * Rebuild milestone entries
     */
    function rebuild_milestones() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ApplicationObjects::rebuildProjectObjectContexts(array('Milestone'), 'milestone');
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_milestones
    
  }