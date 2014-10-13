<?php

	// We need admin controller
  AngieApplication::useController('admin');
  
  /**
   * Administration settings for project requests
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectRequestsAdminController extends AdminController {
  	
  	/**
  	 * Project requests settings
  	 */
  	function index() {
  	  if($this->request->isAsyncCall()) {
  	    $gd_loaded = (extension_loaded('gd') || extension_loaded('gd2')) && function_exists('imagefttext');
  	    
  	    $project_request_data = $this->request->post('project_request');
    		if(!is_array($project_request_data)) {
    			$configs = ConfigOptions::getValue(array(
    				'project_requests_enabled',
    				'project_requests_page_title',
    				'project_requests_page_description',
    				'project_requests_custom_fields',
    				'project_requests_captcha_enabled',
    				'project_requests_notify_user_ids'
    			));
    			
    			$project_request_data = array(
    				'project_requests_enabled' => array_var($configs, 'project_requests_enabled'),
    				'project_requests_page_title' => array_var($configs, 'project_requests_page_title'),
    				'project_requests_page_description' => array_var($configs, 'project_requests_page_description'),
    				'project_requests_custom_fields' => array_var($configs, 'project_requests_custom_fields'),
    				'project_requests_captcha_enabled' => $gd_loaded && array_var($configs, 'project_requests_captcha_enabled'),
    				'project_requests_notify_user_ids' => array_var($configs, 'project_requests_notify_user_ids')
    			);
    		} // if
  
    		$this->smarty->assign(array(
    			'project_request_data' => $project_request_data,
    			'gd_loaded' => $gd_loaded
    		));
  
    		if($this->request->isSubmitted()) {

          $custom_fields = array_var($project_request_data, 'custom_fields');
          if (is_foreachable($custom_fields)) {
            foreach ($custom_fields as $key => $custom_field) {
              if ($custom_field['enabled'] == "1" &&  strlen(trim(array_var($custom_field, 'name'))) === 0) {
                $custom_fields[$key]['enabled'] = "0";
              } // if
            } // foreach
          } // if

    			$new_data = array(
    				'project_requests_enabled' => (boolean) array_var($project_request_data, 'enabled'),
    				'project_requests_page_title' => array_var($project_request_data, 'page_title'),
    				'project_requests_page_description' => array_var($project_request_data, 'page_description'),
    				'project_requests_custom_fields' => $custom_fields,
    				'project_requests_captcha_enabled' => !$gd_loaded ? false : $project_request_data['captcha_enabled'] ? $project_request_data['captcha_enabled'] : false,
    				'project_requests_notify_user_ids' => array_var($project_request_data, 'notify_user_ids')
    			);
    			
    			ConfigOptions::setValue($new_data);
    			
//    			$this->response->respondWithData($new_data, array(
//    			  'as' => 'project_requests_settings', 
//    			));
    			$this->response->ok();
    		} // if
  	  } else {
  	    $this->response->badRequest();
  	  } // if
  	} // index
  	
  }