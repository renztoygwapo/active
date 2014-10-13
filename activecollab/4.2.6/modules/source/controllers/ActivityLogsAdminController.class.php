<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild source entries
     */
    function rebuild_source() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          //ActivityLogs::rebuildProjectObjectActivityLogs(array('Task'), 'tasks', array('Task' => 'task/created'), array('Task' => 'task/completed'), true, true);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_source
    
  }