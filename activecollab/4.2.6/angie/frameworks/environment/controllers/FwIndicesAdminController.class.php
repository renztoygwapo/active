<?php

  // Build on top of admin framework
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Incides administration controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwIndicesAdminController extends AdminController {
  
    /**
     * Display all indices
     */
    function index() {
      $indices = array(array(
        'name' => lang('Context Cache'), 
        'description' => lang('Cache where current context of each object is remembered. This index is used to quickly know when objects are moved between contexts'), 
        'icon' => AngieApplication::getImageUrl('contexts-index.png', ENVIRONMENT_FRAMEWORK), 
        'rebuild_url' => Router::assemble('object_contexts_admin_rebuild'), 
        'size' => ApplicationObjects::calculateObjectContextsIndexSize(), 
      ));
      
      EventsManager::trigger('on_all_indices', array(&$indices));
      
      $this->response->assign('all_indices', $indices);
    } // index
    
    /**
     * Rebuild all indices...
     */
    function rebuild() {
      $steps = ApplicationObjects::getRebuildContextsActions();
      
      EventsManager::trigger('on_rebuild_all_indices', array(&$steps, $this->request->get('quick')));
      
      $steps[Router::assemble('indices_admin_rebuild_finish')] = lang('Finish');

      if ($this->request->get('return_steps')) {
        $this->response->respondWithMap($steps);
      } else {
        $this->response->assign('all_steps', $steps);
      } // if
    } // rebuild
    
    /**
     * Finish rebuild process
     */
    function rebuild_finish() {
      ConfigOptions::setValue('require_index_rebuild', false);
      $this->response->ok();
    } // rebuild_finish
    
  }