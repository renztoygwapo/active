<?php

  // Build on top of indices admin controller
  AngieApplication::useController('indices_admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level object contexts controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwObjectContextsAdminController extends IndicesAdminController {
  
    /**
     * Prepare and render rebuild activity logs actions list
     */
    function rebuild() {
      $this->response->assign('actions', ApplicationObjects::getRebuildContextsActions());
    } // rebuild
    
    /**
     * Wipe all activity logs
     */
    function clean() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ApplicationObjects::cleanUpContexts();
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // clean
    
  }