<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Project settings administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectsAdminController extends AdminController {
  
    /**
     * Show and process project settings form
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $settings_data = $this->request->post('settings', ConfigOptions::getValue(array(
          'morning_paper_enabled',
          'project_tabs',
          'default_project_object_visibility',
          'clients_can_delegate_to_employees',
          'mail_to_project',
          'mail_to_project_default_action'
        )));

        $this->response->assign('settings_data', $settings_data);

        if($this->request->isSubmitted()) {
          try {
            ConfigOptions::setValue(array(
              'morning_paper_enabled' => (boolean) $settings_data['morning_paper_enabled'],
            	'project_tabs' => (array) $settings_data['project_tabs'],
              'default_project_object_visibility' => (integer) $settings_data['default_project_object_visibility'], 
              'clients_can_delegate_to_employees' => (boolean) $settings_data['clients_can_delegate_to_employees'],
              'mail_to_project' => (boolean) $settings_data['mail_to_project'],
              'mail_to_project_default_action' => $settings_data['mail_to_project_default_action']
        		));

            CustomFields::setCustomFieldsByType('Project', $settings_data['custom_fields']);

            AngieApplication::cache()->clear();
        		
        		$this->response->respondWithData($settings_data);
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
  }