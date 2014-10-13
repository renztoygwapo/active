<?php

  /**
   * Framework level state controller
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwStateController extends Controller {
    
    /**
     * Selected object
     *
     * @var ApplicationObject|IState
     */
    protected $active_object;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->request->isApiCall() || $this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if(!($this->active_object instanceof IState && $this->active_object->isLoaded())) {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * Archive selected object
     */
    function state_archive() {
    	if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->state()->canArchive($this->logged_user)) {
          try {
            $this->active_object->state()->archive();
            
            if($this->request->isPageCall()) {
            	$this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
              	'detailed' => true
              ));
            } // if
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Restore selected object from archive
     */
    function state_unarchive() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->state()->canUnarchive($this->logged_user)) {
          try {
            $this->active_object->state()->unarchive();
            
            if($this->request->isPageCall()) {
            	$this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
                'detailed' => true
              ));
            } // if
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // unarchive
    
    /**
     * Move selected object to trash
     */
    function state_trash() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->state()->canTrash($this->logged_user)) {
          try {
            $this->active_object->state()->trash();
            
            if($this->request->isPageCall()) {
            	$this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
                'detailed' => true
              ));
            } // if
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // trash
    
    /**
     * Restore selected object from trash
     */
    function state_untrash() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->state()->canUntrash($this->logged_user)) {
          try {
            $this->active_object->state()->untrash();
            
            if($this->request->isPageCall()) {
            	$this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($this->active_object, array(
                'as' => $this->active_object->getBaseTypeName(),
                'detailed' => true
              ));
            } // if
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // untrash
    
    /**
     * Mark selected object as deleted
     */
    function state_delete() {
      if($this->request->isSubmitted() && ($this->request->isApiCall() || $this->request->isAsyncCall())) {
        if($this->active_object->getState() > STATE_DELETED && $this->active_object->state()->canDelete($this->logged_user)) {
          try {
            $this->active_object->state()->delete();

            $this->response->respondWithData($this->active_object->getId());
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }