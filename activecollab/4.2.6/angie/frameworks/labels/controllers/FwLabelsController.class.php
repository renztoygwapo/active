<?php

  /**
   * Framework level labels controller implementation
   * 
   * @package angie.frameworks.labels
   * @subpackage controllers
   */
  abstract class FwLabelsController extends Controller {
    
    /**
     * Parent object instance
     * 
     * @var ILabel
     */
    protected $active_object;
    
    /**
     * Prepare controller before action is being executed
     */
    function __before() {
      if ($this->active_object instanceof ILabel) {
        if ($this->active_object->isLoaded()) {
          $this->response->assign(array(
            'active_object' => $this->active_object, 
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->notFound();        
      } // if
    } // __before
    
    /**
     * Update label
     */
    function update_label() {
      if ($this->request->isAsyncCall()) {
        if ($this->active_object->canEdit($this->logged_user)) {
          $object_data = $this->request->post('object', array(
            'label_id' => $this->active_object->getLabelId(),
          ));
          
          $this->response->assign('object_data', $object_data);
          
          if ($this->request->isSubmitted()) {
            try {
              DB::beginWork('Updating label');
              $this->active_object->setAttributes($object_data);
              $this->active_object->save();
              DB::commit('Label Updated');
              
              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
                'detailed' => true
              ));
            } catch (Exception $e) {
              DB::rollback('Failed to save changes to label @ ' . __CLASS__);
    
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // update_label
    
  }