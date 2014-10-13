<?php

  // Extend search index admin controller
  AngieApplication::useController('search_index_admin', AUTHENTICATION_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level help search index admin controller implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwHelpSearchIndexAdminController extends SearchIndexAdminController {
    
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof HelpSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          AngieApplication::help()->buildSearchIndex($this->active_search_index);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // build
  
  }