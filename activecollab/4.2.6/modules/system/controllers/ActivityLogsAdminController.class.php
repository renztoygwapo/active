<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
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
          DB::beginWork('Updating project activity logs @ ' . __CLASS__);
          
          $projects = DB::execute('SELECT id, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'projects WHERE state >= ?', STATE_ARCHIVED);
          if($projects) {
            $projects->setCasting(array(
              'id' => DBResult::CAST_INT,
              'created_by_id' => DBResult::CAST_INT,  
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            foreach($projects as $project) {
              $batch->insert('Project', $project['id'], "projects:projects/$project[id]", 'project/created', null, null, $project['created_on'], $project['created_by_id'], $project['created_by_name'], $project['created_by_email']);
              
              ActivityLogs::rebuildStateChangeActivityLogs('Project', $project['id'], "projects:projects/$project[id]", null, null, 'project');
              ActivityLogs::rebuildCompletionActivityLogs('Project', $project['id'], "projects:projects/$project[id]", null, null, 'project/completed', 'project/reopened');
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Project activity logs updated @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update project activity logs @ ' . __CLASS__);
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
          ActivityLogs::rebuildProjectObjectActivityLogs(array('Milestone'), 'milestones', array('Milestone' => 'milestone/created'), 'milestone/completed', 'milestone/reopened', true);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_milestones
    
  }