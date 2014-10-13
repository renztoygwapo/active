<?php

  // Build on top of indices admin framework
  AngieApplication::useController('indices_admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Activity logs admin controller
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage controllers
   */
  abstract class FwActivityLogsAdminController extends IndicesAdminController {
    
    /**
     * Prepare and render rebuild activity logs actions list
     */
    function rebuild() {
      $this->response->assign('actions', ActivityLogs::getRebuildActions());
    } // rebuild
    
    /**
     * Wipe all activity logs
     */
    function clean() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ActivityLogs::cleanUp();
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // clean
  
  }