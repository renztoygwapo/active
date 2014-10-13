<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', SYSTEM_MODULE);
  
  /**
   * Administration settings controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SettingsController extends AdminController {
       
    /**
     * General settings
     */
    function general() {
      if($this->request->isAsyncCall()) {

        $general_data = $this->request->post('general');
        if (!is_array($general_data)) {
          $config_options = array();

          if (!AngieApplication::isOnDemand()) {
            $config_options[] = 'help_improve_application';
            $config_options[] = 'on_logout_url';
          } // if

          if(AngieApplication::isModuleLoaded('tracking')) {
            $config_options[] = 'default_billable_status';
          } // if

          if (is_foreachable($config_options)) {
            $general_data = ConfigOptions::getValue($config_options);
            $general_data['use_on_logout_url'] = isset($general_data['on_logout_url']) && $general_data['on_logout_url'] && is_valid_url($general_data['on_logout_url']);
          } // if

        } // if
      	$this->smarty->assign('general_data', $general_data);
      	
      	if($this->request->isSubmitted()) {
          if (!AngieApplication::isOnDemand()) {
            if($this->request->post('use_on_logout_url')) {
              $logout_url = trim($general_data['on_logout_url']);
              if($logout_url) {
                if(strpos($logout_url, '://') === false) {
                  $logout_url = "http://$logout_url";
                } // if

                if(!is_valid_url($logout_url)) {
                  $logout_url = '';
                } // if
              } // if

              ConfigOptions::setValue('on_logout_url', $logout_url);
            } else {
              ConfigOptions::setValue('on_logout_url', null);
            } // if

            ConfigOptions::setValue('help_improve_application', (boolean) $this->request->post('help_improve_application'));
          } // if

          if(AngieApplication::isModuleLoaded('tracking')) {
            $default_billable_status = (integer) $this->request->post('default_billable_status');

            if($default_billable_status !== 1) {
              $default_billable_status = 0;
            } // if

            ConfigOptions::setValue('default_billable_status', $default_billable_status, true);
          } // if
      		
      		$this->response->ok();
      	} // if
      } else {
        $this->response->badRequest();
      } // if
    } // general
    
  }