<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Theme admin controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwThemeAdminController extends AdminController {
  
    /**
     * Show and process project settings form
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $settings_data = $this->request->post('settings', ConfigOptions::getValue(array(
          'theme',  
        )));
        
        $this->response->assign('settings_data', $settings_data);
        
        if($this->request->isSubmitted()) {
          try {
            ConfigOptions::setValue(array(
            	'theme' => $settings_data['theme'],  
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