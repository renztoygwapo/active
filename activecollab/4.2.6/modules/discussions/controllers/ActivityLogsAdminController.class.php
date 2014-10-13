<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.discussions
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild task entries
     */
    function rebuild_discussions() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ActivityLogs::rebuildProjectObjectActivityLogs(array('Discussion'), 'discussions', array('Discussion' => 'discussion/created'), null, null, true);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_discussions
    
  }