<?php

  // Use search admin controller
  AngieApplication::useController('indices_admin', SEARCH_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level search index administration controller
   * 
   * @package angie.frameworks.search
   * @subpackage controllers
   */
  abstract class FwSearchIndexAdminController extends IndicesAdminController {
    
    /**
     * Search index instance
     *
     * @var SearchIndex
     */
    protected $active_search_index;
    
    /**
     * Execute before any of the actions
     */
    function __before() {
      if($this->request->isAsyncCall()) {
        $search_index_name = $this->request->get('search_index_name');
        
        if($search_index_name) {
          try {
            $this->active_search_index = Search::getIndex($search_index_name);
          } catch(InvalidParamError $e) {
            $this->response->notFound();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
        
        if(!($this->active_search_index instanceof SearchIndex)) {
          $this->response->notFound();
        } // if
        
        $this->response->assign('active_search_index', $this->active_search_index);
      } else {
        $this->response->badRequest();
      } // if
    } // __before
  
    /**
     * Provide search index rebuild strategy and see that it get executed
     */
    function rebuild() {
      
    } // rebuild
    
    /**
     * Re-initialize index
     */
    function reinit() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          if($this->active_search_index->isInitialized()) {
            $this->active_search_index->tearDown();
          } // if
          
          $this->active_search_index->initialize();
          
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // reinit
    
  }