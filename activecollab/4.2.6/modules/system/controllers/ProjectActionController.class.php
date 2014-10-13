<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Task Action controller
   * 
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class ProjectActionController extends BackendController {

    /**
     * Return elements when project change
     */
    function project_change() {
      $project_id = $this->request->post('project_id');
      
      $project = Projects::findById($project_id);
      if(!$project instanceof Project) {
        throw new Error(HTTP_ERR_NOT_FOUND,lang('Project not found.'));
      }//if
      
      $filter_id = $this->request->post('filter_id');
      if($filter_id && intval($filter_id)) {
        $filter = IncomingMailFilters::findById($filter_id);
      }//if
      
      $action_class_name = $this->request->post('action_class_name');
      
      $action = new $action_class_name();
      $elements = $action->renderProjectElements($this->logged_user, $project, $filter);
      
      $this->response->respondWithData($elements,array('as' => 'elements'));
     
    } //project_change
    
  }//ProjectActionController