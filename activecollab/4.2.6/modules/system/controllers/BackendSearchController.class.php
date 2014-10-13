<?php

  // Build on top of framework level implementation
  AngieApplication::useController('fw_backend_search', SEARCH_FRAMEWORK);

  /**
   * Backend search controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class BackendSearchController extends FwBackendSearchController {
  
    /**
     * activeCollab specific quick search implementation
     */
    function quick_search() {
    	
    	// Asynchronous request
      if($this->request->isAsyncCall()) {
        $search_for = trim($this->request->get('q'));

        if($search_for) {
          $project_id = $this->request->get('project_id', null);
          $search_criterions = array( new SearchCriterion('short_name', SearchCriterion::IS, $search_for, SearchCriterion::EXTEND_RESULT) );

          if ($project_id) {
            $search_criterions[] = new SearchCriterion('item_context', SearchCriterion::LIKE, 'projects:projects/' . $project_id . '/%', SearchCriterion::FILTER_RESULT);
          } // if

          $result = Search::queryPaginated($this->logged_user, 'names', $search_for, $search_criterions, 1, 30);
        } else {
          $result = array();
        } // if
        
        $this->response->respondWithData($result, array('as' => 'search_results'));
        
      // Request made by phone device
      } elseif($this->request->isPhone()) {
      	$search_for = trim($this->request->post('q'));

        if($search_for) {
          $project_id = $this->request->get('project_id', null);
          $search_criterions = array( new SearchCriterion('short_name', SearchCriterion::IS, $search_for, SearchCriterion::EXTEND_RESULT) );

          if ($project_id) {
            $search_criterions[] = new SearchCriterion('item_context', SearchCriterion::LIKE, 'projects:projects/' . $project_id . '/%', SearchCriterion::FILTER_RESULT);
          } // if

          $result = Search::query($this->logged_user, 'names', $search_for, $search_criterions);
        } else {
          $result = array();
        } // if
        
        $this->response->assign(array(
        	'search_for' => $search_for,
        	'search_results' => $result
        ));
      	
      } else {
        $this->response->badRequest();
      } // if
    } // quick_search
    
  }