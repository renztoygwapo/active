<?php

  /**
   * Framework level priority controller implementation
   * 
   * @package angie.frameworks.complete
   * @subpackage controllers
   */
  abstract class FwPriorityController extends Controller {
    
    /**
     * Parent object instance
     * 
     * @var IComplete
     */
    protected $active_object;
    
    /**
     * Prepare controller before action is being executed
     */
    function __before() {
      if (!($this->active_object instanceof IComplete)) {
        $this->response->notFound();        
      } // if
      
      if (!$this->active_object->fieldExists('priority')) {
      	$this->response->notFound();
      } // if
      
      if ($this->active_object->isNew()) {
        $this->response->notFound();
      } // if
        
      $this->response->assign(array(
        'active_object' => $this->active_object, 
      ));
    } // __before
    
    /**
     * Update priority
     */
    function update_priority() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if
      
      if (!$this->active_object->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      $object_data = $this->request->post('object', array(
        'priority' => $this->active_object->getPriority(),
      ));
      
      $this->smarty->assign('object_data', $object_data);
     
      if ($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating priority');
          $this->active_object->setAttributes($object_data);
          $this->active_object->save();
          DB::commit('Priority Updated');
          
          $this->response->respondWithData($this->active_object, array(
            'as' => $this->active_object->getBaseTypeName(),
            'detailed' => true
          ));
        } catch (Exception $e) {
          DB::rollback('Failed to save changes to priority @ ' . __CLASS__);

          $this->response->exception($e);
        }
      } // if
    } // update_priority
    
  }