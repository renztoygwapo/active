<?php

  // Build on top of administration controller
  AngieApplication::useController('settings', SYSTEM_MODULE);

  /**
   * Categories settings controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CategoriesAdminController extends SettingsController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('master_categories', lang('Master Categories'), Router::assemble('admin_settings_categories'));
    } // __construct
    
    /**
     * Show and process manage categories page
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $category_definitions = array();
        EventsManager::trigger('on_master_categories', array(&$category_definitions));
        
        $this->smarty->assign('category_definitions', $category_definitions);
        
        if($this->request->isSubmitted()) {
          try {
            if(is_foreachable($category_definitions)) {
              foreach($category_definitions as $category_definition) {
                $value = $this->request->post($category_definition['name']);
                
                if(is_foreachable($value)) {
                  ConfigOptions::setValue($category_definition['name'], $value);
                } else {
                  ConfigOptions::setValue($category_definition['name'], array());
                } // if
              } // foreach
            } // if
            
            $this->response->ok();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
  }