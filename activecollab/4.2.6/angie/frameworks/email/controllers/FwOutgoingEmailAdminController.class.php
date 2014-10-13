<?php

  // Build on top of mailing controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level outgoing email administration
   *
   * @package angie.framework.email
   * @subpackage controllers
   */
  abstract class FwOutgoingEmailAdminController extends EmailAdminController {
    
    /**
     * Show and process outgoing email settings pages
     */
    function settings() {
      if (AngieApplication::isOnDemand()) {
        $this->response->notFound();
      } // if

      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        $this->response->assign('admin_email', ADMIN_EMAIL);
      
      	$mailing_data = $this->request->post('mailing');
    		
      	if(!is_array($mailing_data)) {
    		  $mailing_data = array(
    		    'mailing' => AngieApplication::mailer()->getConnectionType()
    		  );
    		  
    		  $mailing_data = array_merge($mailing_data, ConfigOptions::getValue(array(
      		  'mailing_method',
      		  'mailing_method_override',
      		)));
      		
      		if(empty($mailing_data['mailing_method'])) {
      		  $mailing_data['mailing_method'] = AngieMailerDelegate::SEND_INSTANTNLY;
      		} // if
    		  
    		  if(!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
    		    $mailing_data = array_merge($mailing_data, ConfigOptions::getValue(array(
    		      'mailing', 
        		  'mailing_smtp_host', 
        		  'mailing_smtp_port', 
        		  'mailing_smtp_authenticate', 
        		  'mailing_smtp_username', 
        		  'mailing_smtp_password', 
        		  'mailing_smtp_security', 
    		    	'mailing_native_options', 
    		    )));
    		  } // if
    		  
    		  if(!AngieApplication::mailer()->isMessageConfigurationLocked()) {
    		    $mailing_data = array_merge($mailing_data, ConfigOptions::getValue(array(
    		      'mailing_mark_as_bulk',
          	  'notifications_from_force', 
        		  'notifications_from_email',
        		  'notifications_from_name', 
    		    )));
    		    
    		    if(!is_valid_email($mailing_data['notifications_from_email'])) {
        		  $mailing_data['notifications_from_email'] = ADMIN_EMAIL;
        		} // if
    		  } // if
    		} // if
      	
      	$this->response->assign('mailing_data', $mailing_data);
      	
      	if($this->request->isSubmitted()) {
      	  try {
      	    DB::beginWork('Updating mailing configuration @ ' . __CLASS__);
      	    
      	    $errors = new ValidationErrors();
      	    
      	    // Validate SMTP connection parameters
      	    if(!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
      	      if($mailing_data['mailing'] == 'smtp') {
          	    if(empty($mailing_data['mailing_smtp_host'])) {
          	    	$errors->addError('SMTP host is required', 'mailing_smtp_host');
          	    } // if
          	    
          	    if(empty($mailing_data['mailing_smtp_port'])) {
          	    	$errors->addError('SMTP port is required', 'mailing_smtp_port');
          	    } // if
          	    
          	    // only for smtp authentication check username & password
                if($mailing_data['mailing_smtp_authenticate']) {
                  if(empty($mailing_data['mailing_smtp_username'])) {
                  	$errors->addError('SMTP username is required', 'mailing_smtp_username');
                  } // if
                  if(empty($mailing_data['mailing_smtp_password'])) {
                  	$errors->addError('SMTP password is required', 'mailing_smtp_password');
                  } // if
                } // if
          	  } // if
      	    } // if
      	    
      	    // Validate from email address
      	    if(!AngieApplication::mailer()->isMessageConfigurationLocked()) {
              if(isset($mailing_data['notifications_from_email']) && $mailing_data['notifications_from_email']) {
                if(!is_valid_email($mailing_data['notifications_from_email'])) {
                  $errors->addError('Email address is not valid', 'notifications_from_email');
                } // if
              } // if
      	    } // if
            
            if($errors->hasErrors()) {
      	      throw $errors;
      	    } // if
      	    
      	    if(!AngieApplication::mailer()->isConnectionConfigurationLocked()) {
        	    ConfigOptions::setValue(array(
        	      'mailing' => (string) $mailing_data['mailing'], 
        	      'mailing_smtp_host' => (string) $mailing_data['mailing_smtp_host'], 
        	      'mailing_smtp_port' => (integer) $mailing_data['mailing_smtp_port'], 
        	      'mailing_smtp_authenticate' => (boolean) $mailing_data['mailing_smtp_authenticate'], 
        	      'mailing_smtp_username' => (string) $mailing_data['mailing_smtp_username'], 
        	      'mailing_smtp_password' => (string) $mailing_data['mailing_smtp_password'], 
        	      'mailing_smtp_security' => (string) $mailing_data['mailing_smtp_security'],
        	    	'mailing_native_options' => (string) $mailing_data['mailing_native_options'], 
        	    ));
      	    } // if
      	    
      	    if(!AngieApplication::mailer()->isMessageConfigurationLocked()) {
      	      ConfigOptions::setValue(array( 
        	      'mailing_mark_as_bulk' => (boolean) $mailing_data['mailing_mark_as_bulk'], 
        	      'notifications_from_force' => (boolean) $mailing_data['notifications_from_force'], 
        	      'notifications_from_email' => trim($mailing_data['notifications_from_email']), 
        	      'notifications_from_name' => (string) $mailing_data['notifications_from_name'], 
        	    ));
      	    } // if
      	    
      	    ConfigOptions::setValue(array(
      	      'mailing_method' => (string) $mailing_data['mailing_method'], 
      	      'mailing_method_override' => (boolean) $mailing_data['mailing_method_override'], 
      	    ));

            AngieApplication::cache()->remove('config_options');
      	    
      	    DB::commit('Mailing configuration updated @ ' . __CLASS__);
      	    
      	    $this->response->respondWithData(array(
        	    'mailing' => ConfigOptions::getValue('mailing'), 
        		  'mailing_smtp_host' => ConfigOptions::getValue('mailing_smtp_host'), 
        		  'mailing_smtp_port' => (integer) ConfigOptions::getValue('mailing_smtp_port'), 
        		  'mailing_smtp_authenticate' => ConfigOptions::getValue('mailing_smtp_authenticate'),
        		  'mailing_smtp_username' => ConfigOptions::getValue('mailing_smtp_username'), 
        		  'mailing_smtp_password' => ConfigOptions::getValue('mailing_smtp_password'), 
        		  'mailing_smtp_security' => ConfigOptions::getValue('mailing_smtp_security'),
        		  'mailing_method' => ConfigOptions::getValue('mailing_method'),
        		  'mailing_method_override' => (boolean) ConfigOptions::getValue('mailing_method_override'),
        		  'mailing_native_options' => ConfigOptions::getValue('mailing_native_options'),
        		  'mailing_mark_as_bulk' => (boolean) ConfigOptions::getValue('mailing_mark_as_bulk'),
        		  'notifications_from_force' => ConfigOptions::getValue('notifications_from_force'),
        		  'notifications_from_email' => ConfigOptions::getValue('notifications_from_email'),
        		  'notifications_from_name' => ConfigOptions::getValue('notifications_from_name'), 
        	  ), 'mailing_settings');
      	  } catch(Exception $e) {
      	    DB::rollback('Failed to update mailing configuration @ ' . __CLASS__);
      	    $this->response->exception($e);
      	  } // try
      	} // if
      } else {
        $this->response->badRequest();
      } // if
    } // settings
    
    /**
     * Quckly test SMTP connection parameters
     */
    function test_smtp_connection() {
      if (AngieApplication::isOnDemand()) {
        $this->response->notFound();
      } // if

      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $mailing_data = $this->request->post('mailing');
      	if(empty($mailing_data)) {
      	  $this->response->operationFailed();
      	} // if
      	  	
      	$host = array_var($mailing_data,'mailing_smtp_host');
      	$port = (integer) array_var($mailing_data,'mailing_smtp_port', 25);
      	$security = array_var($mailing_data, 'mailing_smtp_security');
      	
      	if($security != 'ssl' && $security != 'tls') {
      	 $security = null;
      	} // if
      	
      	$authenticate = (boolean) array_var($mailing_data,'mailing_smtp_authenticate');
        $username = array_var($mailing_data, 'mailing_smtp_username');
        $password = array_var($mailing_data, 'mailing_smtp_password');
        
        
        $message = null;
        $test = AngieApplication::mailer()->testSmtp($host, $port, $message, $authenticate, $username, $password, $security);
        
        if($test === true) {
          $result = array(
            'message' => $message,
            'isSuccess' => true,
          );
        } else {
          $result = array(
            'message' => lang('Failed to connect. Reason: :reason', array('reason' => $message)),
            'isSuccess' => false,
          );
        } // if
        
        $this->response->respondWithData($result, array(
          'as' => 'result', 
          'format' => BaseHttpResponse::JSON, 
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // test_smtp_connection
    
    /**
     * Send test message
     */
    function send_test_message() {
      if (AngieApplication::isOnDemand()) {
        $this->response->notFound();
      } // if

      $email_data = $this->request->post('email');
      if(!is_array($email_data)) {
        $email_data = array(
          'recipient' => $this->logged_user->getEmail(),
          'subject' => lang('activeCollab - test email'),
          'message' => lang("<p>Hi,</p>\n\n<p>Purpose of this message is to test whether activeCollab can send emails or not</p>"),
        );
      } // if
      
      list($from_email, $from_name) = AngieApplication::mailer()->getFromEmailAndName();
      
      $this->response->assign(array(
        'email_data' => $email_data,
        'test_email_from' => $from_name ? "$from_name <$from_email>" : $from_email,
      ));
    	if($this->request->isSubmitted()) {
    	  $errors = new ValidationErrors();
    	  
    	  $subject = trim(array_var($email_data, 'subject'));
    	  $message = trim(array_var($email_data, 'message'));
    	  $recipient = trim(array_var($email_data, 'recipient'));
    	  
    	  if($subject == '') {
    	    $errors->addError(lang('Message subject is required'), 'subject');
    	  } // if
    	  
    	  if($message == '') {
    	    $errors->addError(lang('Message body is required'), 'message');
    	  } // if
    	  
    	  if(is_valid_email($recipient)) {
    	    $recipient_name = null;
    	    $recipient_email = $recipient;
    	  } else {
    	    if((($pos = strpos($recipient, '<')) !== false) && str_ends_with($recipient, '>')) {
    	      $recipient_name = trim(substr($recipient, 0, $pos));
    	      $recipient_email = trim(substr($recipient, $pos + 1, strlen($recipient) - $pos - 2));
    	      
    	      if(!is_valid_email($recipient_email)) {
    	        $errors->addError(lang('Invalid email address'), 'recipient');
    	      } // if
    	    } else {
    	      $errors->addError(lang('Invalid recipient'), 'recipient');
    	    } // if
    	  } // if
    	  
    	  if($errors->hasErrors()) {
    	  	AngieApplication::mailer()->disconnect();
    	    
    	    $this->response->assign('errors', $errors);
    	    $this->render();
    	  } // if
    	  
  	    if(AngieApplication::mailer()->doSend(new AnonymousUser($recipient_email, $recipient_name), $subject, $message)) {
  	      $this->flash->success('Test message has been sent. Please check your inbox');
  	    } else {
  	      $this->flash->error('Failed to send test message. Please make sure that your Outgoing Mail is properly configured');
  	    } // if
    	  
    	  $this->response->redirectTo('outgoing_email_admin_send_test_message');
    	} // if    	
    } // send_test_message
    
  }