<?php

  /**
   * Move to project controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class MoveToProjectController extends Controller {
    
    /**
     * Active parent object
     *
     * @var ProjectObject
     */
    protected $active_object;
    
    /**
     * Active project
     *
     * @var Project
     */
    protected $active_project;
    
    /**
     * Prepare controller before action is being executed
     *
     * @param Request $request
     */
    function __before() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_object instanceof ProjectObject) {
          $this->response->assign('active_object', $this->active_object);

          $this->response->assign('redirect_to_target_project', (bool) Cookies::getVariable('redirect_to_target_project'));
          if ($this->request->isSubmitted()) {
            $redirect_to_target_project = (bool) $this->request->post('redirect_to_target_project', false);
            Cookies::setVariable("redirect_to_target_project", $redirect_to_target_project, null, false);
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * Move object to project
     */
    function move_to_project() {
      if($this->active_object->canMove($this->logged_user)) {
        if($this->request->isSubmitted()) {
          $move_to_project_id = (integer) $this->request->post('move_to_project_id');
          $move_to_project = $move_to_project_id ? Projects::findById($move_to_project_id) : null;
          
          if($move_to_project instanceof Project) {
            try {

              $additional_params = $this->request->post('additional_params');
              $this->active_object->moveToProjectAndPreserveCategory($move_to_project, $additional_params);
              $this->response->respondWithData(ProjectObjects::findById($this->active_object->getId()), array(
                'as' => $this->active_object->getBaseTypeName(),
              	'detailed' => true
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->notFound();
          } // if
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // move_to_project
    
    /**
     * Copy object to project
     */
    function copy_to_project() {
      if($this->active_object->canCopy($this->logged_user)) {

        if($this->request->isSubmitted()) {
          $copy_to_project_id = (integer) $this->request->post('copy_to_project_id');
          $copy_to_project = $copy_to_project_id ? Projects::findById($copy_to_project_id) : null;

          if($copy_to_project instanceof Project) {
            try {
              $copy = $this->active_object->copyToProjectAndPreserveCategory($copy_to_project);
              
              if($copy instanceof ProjectObject) {
                $this->response->respondWithData($copy, array(
                  'as' => $this->active_object->getBaseTypeName(),
                  'detailed' => true
                ));
              } else {
                $this->response->operationFailed();
              } // if
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->notFound();
          } // if
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // copy_to_project
  
  }