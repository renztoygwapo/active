<?php

  // Inherit application level controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level mailbox management controller
   *
   * @package angie.frameworks.email
   * @subpackage controllers
   */
  abstract class FwIncomingMailAdminController extends EmailAdminController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
     
    } // __construct
    
    /**
     * Mailbox index
     */
    function settings() {
      
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        
        $settings_data = $this->request->post('settings');
        
        if(!is_array($settings_data)) {
          if (!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
            $settings_data = array(
              'disable_mailbox_on_successive_connection_failures' => ConfigOptions::getValue('disable_mailbox_on_successive_connection_failures', false),
              'successive_connection_attempts' => ConfigOptions::getValue('disable_mailbox_successive_connection_attempts', false),
              'notify_administrator_when_mailbox_is_disabled' => (integer) ConfigOptions::getValue('disable_mailbox_notify_administrator', false)
            );
          } //if

          $settings_data['conflict_notifications_delivery']  = ConfigOptions::getValue('conflict_notifications_delivery', false);
        }//if
        
        $this->response->assign('settings_data', $settings_data);
        
        if($this->request->isSubmitted()) {
          try {
      	    DB::beginWork('Updating incoming mail configuration @ ' . __CLASS__);

            if (!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
              ConfigOptions::setValue(array(
                'disable_mailbox_on_successive_connection_failures' => (integer) $settings_data['disable_mailbox_on_successive_connection_failures'],
                'disable_mailbox_successive_connection_attempts' => (integer) $settings_data['successive_connection_attempts'],
                'disable_mailbox_notify_administrator' => (integer) $settings_data['notify_administrator_when_mailbox_is_disabled']
              ));
            } //if

      	    ConfigOptions::setValue(array(
        	      'conflict_notifications_delivery' => (integer) $settings_data['conflict_notifications_delivery'],
      	    ));
        	  
            DB::commit('Incoming configuration updated @ ' . __CLASS__);

            if(!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
              $response = array(
                'disable_mailbox_on_successive_connection_failures' => ConfigOptions::getValue('disable_mailbox_on_successive_connection_failures', false),
                'successive_connection_attempts' => ConfigOptions::getValue('disable_mailbox_successive_connection_attempts', false),
                'notify_administrator_when_mailbox_is_disabled' => ConfigOptions::getValue('disable_mailbox_notify_administrator', false)
              );
            } //if

            $response['conflict_notifications_delivery'] = ConfigOptions::getValue('conflict_notifications_delivery');
            $this->response->respondWithData($response, 'settings');
        	  
      	  } catch (Exception $e) {
      	    DB::rollback('Failed to update incoming mail configuration @ ' . __CLASS__);
      	    $this->response->exception($e);
      	  }//try
        }//if
      } else {
        $this->response->badRequest();
      }//if
    }//settings
    
    
  }//FwIncomingMailAdminController