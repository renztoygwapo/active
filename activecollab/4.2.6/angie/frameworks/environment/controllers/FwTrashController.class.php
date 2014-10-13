<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level trash controller implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwTrashController extends BackendController {
    
    /**
     * Active object
     * 
     * @var IState
     */
    var $active_object;
    
    /**
     * Execute before any of the action
     */
    function __before() {
      parent::__before();
      
      if (!Trash::canAccess($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->breadcrumbs->add('trash', lang('Trash'), Router::assemble('trash'));
      
      $type = $this->request->get('object_type');
      $id = $this->request->get('object_id');
        
      if ($id && $type && class_exists($type) && is_subclass_of($type, 'ApplicationObject')) {
        $this->active_object = DataObjectPool::get($type, $id);

        if (!($this->active_object instanceof IState)) {
          $this->response->badRequest();
        } // if
      } // if        
    } // __before
  
    /**
     * Show trash index
     */
    function index() {
      $this->response->assign(array(
        'trash_sections'  => Trash::getSections($this->logged_user),
        'refresh'         => $this->request->get('refresh', false),
        'restore_url'     => Router::assemble('object_untrash', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--')),
        'delete_url'      => Router::assemble('object_delete', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--')),
      ));

      if ($this->request->isSubmitted()) {
        $items = $this->request->post('items');
        $action = strtolower($this->request->post('action'));
        $result = array();
        
        try {
          if (!in_array($action, array('restore', 'delete'))) {
            throw new Error(lang('Action is required'));
          } // if
          
          if (is_foreachable($items)) {
            foreach ($items as $item) {
              $type = array_var($item, 'type', null);
              $id = array_var($item, 'id', null);
              
              if ($id && $type && class_exists($type) && is_subclass_of($type, 'ApplicationObject')) {
                try {
                  $object = DataObjectPool::get($type, $id);
                  if ($object instanceof IState && $object->isLoaded()) {
                    if ($action == 'delete') {
                      $object->state()->delete();
                    } else {
                      $object->state()->untrash();
                    } // if
                    $result[] = $object;
                  } // if
                } catch (Exception $e) {
                  // noop
                } // try
              } // if
            } // foreach
          } // if
          
          $this->response->respondWithData($result, array('detailed' => true));
        } catch (Exception $e) {
          $this->response->exception($e);  
        } // try
      } // if
    } // index
    
    /**
     * Delete permanently all items that are in trash
     */
    function empty_trash() {
      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Emptying trash @ ' . __CLASS__);
          
          Trash::purge($this->logged_user);

          DB::commit('Trash emptied @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to empty trash @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // empty_trash
    
    /**
     * Permanently delete item
     */
    function delete_object() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
      
      if (!$this->active_object || !$this->active_object->isLoaded()) {
        $this->response->notFound();
      } // if
      
      if (!$this->active_object->state()->canDelete($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      try {
        $this->active_object->state()->delete();
        $this->response->respondWithData($this->active_object, array(
          'detailed' => true
        ));
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // delete
    
    /**
     * Untrash item 
     */
    function untrash_object() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
            
      if (!$this->active_object || !$this->active_object->isLoaded()) {
        $this->response->notFound();
      } // if
      
      if (!$this->active_object->state()->canUntrash($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try {
        $this->active_object->state()->untrash();
        $this->response->respondWithData($this->active_object, array(
          'detailed' => true
        ));        
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // unarchive
    
  }