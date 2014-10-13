<?php

  /**
   * Framework level assignees controller implementation
   * 
   * @package angie.frameworks.assignees
   * @subpackage controllers
   */
  abstract class FwAssigneesController extends Controller {
    
    /**
     * Parent object instance
     * 
     * @var ILabel
     */
    protected $active_object;

    /**
     * Exclude users from select assignees
     *
     * @var mixed $exclude_user_ids
     */
    protected $exclude_user_ids;
      
    /**
     * Prepare controller before action is being executed
     */
    function __before() {
      parent::__before();

      if ($this->active_object instanceof IAssignees && $this->active_object->isLoaded()) {
        $this->response->assign(array(
          'active_object' => $this->active_object,
          'exclude_user_ids' => $this->exclude_user_ids
        ));
      } else {
				$this->response->notFound();      	
      } // if
    } // __before
    
    /**
     * Assignees
     */
		function assignees() {
			if ($this->request->isAsyncCall()) {
        if ($this->active_object->canEdit($this->logged_user)) {
          $object_data = $this->request->post('object', array(
            'assignee_id' => $this->active_object->getAssigneeId(),
            'other_assignees' => $this->active_object->assignees()->getOtherAssigneeIds(),
          ));

          $this->smarty->assign('object_data', $object_data);

          if ($this->request->isSubmitted()) {
            try {
              $current_assignee = $this->active_object->assignees()->getAssignee();

              DB::beginWork('Updating assignees @ ' . __CLASS__);

              $this->active_object->setAttributes($object_data);
              $this->active_object->save();

              DB::commit('Assignees Updated @ ' . __CLASS__);

              $this->active_object->assignees()->notifyOnReassignment($current_assignee, $this->active_object->assignees()->getAssignee(), $this->logged_user);

              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
                'detailed' => true
              ));
            } catch (Exception $e) {
              DB::rollback('Failed to save changes to assignees @ ' . __CLASS__);

              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        }	// if
      } else {
				$this->response->badRequest();
			} // if
		} // assignees
    
  }