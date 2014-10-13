<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Object contexts controller
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
    
    /**
     * Rebuild source entries
     */
    function rebuild_source() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ApplicationObjects::rebuildProjectObjectContexts(array('ProjectSourceRepository'), 'source');
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_source
    
  }