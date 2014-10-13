<?php

  // Extend administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Maintenance mode controller
   * 
   * @package angie.frameworks.maintenance_mode
   * @subpackage controllers
   */
  abstract class FwMaintenanceModeController extends AdminController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('maintenance_mode', lang('Maintenance Mode'), Router::assemble('maintenance_mode_settings'));
    } // __construct
    
    /**
     * Display and process maintenance mode form
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $maintenance_data = $this->request->post('maintenance');
        if(!is_array($maintenance_data)) {
          $maintenance_data = ConfigOptions::getValue(array('maintenance_enabled', 'maintenance_message'));
        } // if
        
        $this->smarty->assign('maintenance_data', $maintenance_data);
        
        if($this->request->isSubmitted()) {
          try {
            ConfigOptions::setValue(array(
              'maintenance_enabled' => (boolean) array_var($maintenance_data, 'maintenance_enabled'), 
              'maintenance_message' => trim(array_var($maintenance_data, 'maintenance_message')), 
            ));
            
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