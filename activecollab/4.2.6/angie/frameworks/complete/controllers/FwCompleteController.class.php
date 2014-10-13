<?php

  /**
   * Framework level complete controller delegate implementation
   *
   * @package angie.frameworks.complete
   * @subpackage controllers
   */
  class FwCompleteController extends Controller {
    
    /**
     * Active object instance
     *
     * @var IComplete
     */
    protected $active_object;
    
    /**
     * Stuff that needs to be executed before any of the actions
     */
    function __before() {
      if((($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->isNew()) {
          $this->response->notFound();
        } // if
        
        if(!$this->active_object->complete()->canChangeStatus($this->logged_user)) {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * Mark active object as completed
     */
    function complete() {
      try {
        $this->active_object->complete()->complete($this->logged_user);
        
        if($this->active_object instanceof ISubscriptions) {
          AngieApplication::notifications()
            ->notifyAbout(COMPLETE_FRAMEWORK_INJECT_INTO . '/object_completed', $this->active_object, $this->logged_user)
            ->sendToSubscribers();
        } // if
        
        if($this->request->isPageCall()) {
        	$this->flash->success(':type ":name" has been completed', array(
            'type' => $this->active_object->getVerboseType(),
            'name' => $this->active_object->getName()
          ));
          $this->response->redirectToUrl($this->active_object->getViewUrl());
        } else {
        	$this->response->respondWithData($this->active_object, array(
	          'as' => $this->active_object->getBaseTypeName(),
						'detailed' => true, 
	        ));
        } // if
      } catch(Exception $e) {
        if($this->request->isPageCall()) {
        	$this->smarty->assign('errors', $e);
        } else {
        	$this->response->exception($e);
        } // if
      } // try
    } // complete
    
    /**
     * Mark active object as open
     *
     */
    function reopen() {
      try {
        DB::beginWork('Reopening object @ ' . __CLASS__);
        
        $this->active_object->complete()->open($this->logged_user);
        
        if($this->active_object instanceof ISubscriptions) {
          AngieApplication::notifications()
            ->notifyAbout(COMPLETE_FRAMEWORK_INJECT_INTO . '/object_reopened', $this->active_object, $this->logged_user)
            ->sendToSubscribers();
        } // if
        
        DB::commit('Object reopened @ '. __CLASS__);
        
        if($this->request->isPageCall()) {
        	$this->flash->success(':type ":name" has been reopened', array(
            'type' => $this->active_object->getVerboseType(),
            'name' => $this->active_object->getName()
          ));
          $this->response->redirectToUrl($this->active_object->getViewUrl());
        } else {
        	$this->response->respondWithData($this->active_object, array(
	          'as' => $this->active_object->getBaseTypeName(),
						'detailed' => true,
	        ));
        } // if
      } catch(Exception $e) {
        DB::rollback('Failed to reopen object @ ' . __CLASS__);
        
        if($this->request->isPageCall()) {
        	$this->smarty->assign('errors', $e);
        } else {
        	$this->response->exception($e);
        } // if
      } // try
    } // reopen
    
  }