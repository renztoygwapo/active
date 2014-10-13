<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level search controller implementation
   * 
   * @package angie.frameworks.search
   * @subpackage controllers
   */
  class FwBackendSearchController extends BackendController {
    
    /**
     * Show search index page
     */
    function index() {
      $search = $this->request->get('search');

      if(is_array($search)) {
        if(isset($search['for']) && $search['for'] && isset($search['index']) && $search['index']) {
          $index = Search::getIndex($search['index']);

          if($index instanceof SearchIndex) {
            $criterions = array();

            $filters = isset($search['filters']) && $search['filters'] ? $search['filters'] : null;
            if(is_foreachable($filters)) {
              foreach($filters as $field_name => $field_data) {
                if(is_array($field_data)) {
                  $field_criterion = $field_data['criterion'];
                  $field_value = $field_data['value'];
                } else {
                  $field_criterion = SearchCriterion::IS;
                  $field_value = $field_data;
                } // if

                if($field_criterion == SearchCriterion::IS && empty($field_value)) {
                  continue;
                } // if

                $criterions[] = new SearchCriterion($field_name, $field_criterion, $field_value);
              } // foreach
            } // if

            $results = Search::queryPaginated($this->logged_user, $index, $search['for'], $criterions, $this->request->getPage(), 30);

            if($results) {
              $results[0] = JSON::valueToMap($results[0]);
            } // if

            $this->response->respondWithData($results, array(
              'as' => 'search_results',
            ));
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->operationFailed();
        } // if
      } // if
    } // index
    
  }