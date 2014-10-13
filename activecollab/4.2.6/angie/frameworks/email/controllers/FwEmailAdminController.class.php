<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level mailing administration controller
   *
   * @package angie.frameworks.email
   * @subpackage controllers
   */
  abstract class FwEmailAdminController extends AdminController {
  	
  	/**
  	 * Execute before any of the actions
  	 */
  	function __before() {
  		parent::__before();
  		
  		$this->wireframe->breadcrumbs->add('email_admin', lang('Email'), Router::assemble('email_admin'));
  	} // __before
  	
  	/**
  	 * Show email status page
  	 */
  	function index() {
  	  $inline_tabs = new NamedList();
			$inline_tabs->add('mailing_log', array(
	    	'title'	=> lang('Mailing Log'),
	      'url'		=> Router::assemble('email_admin_logs')
	    ));
			$inline_tabs->add('outgoing_queue', array(
	    	'title'	=> lang('Outgoing Queue'),
	      'url'		=> Router::assemble('outgoing_messages_admin')
	    ));
			$inline_tabs->add('incoming_mail_conflicts', array(
	    	'title'	=> lang('Incoming Mail Conflicts'),
	      'url'		=> Router::assemble('incoming_email_admin_conflict'),
	    ));
	    
	    list($host, $port, $authenticate, $username, $password, $security) = AngieApplication::mailer()->getSmtpConnectionParameters();
  	  
      $this->response->assign(array(
        'mailing' => AngieApplication::mailer()->getConnectionType(),
        'smtp_host' => $host, 
        'smtp_port' => $port, 
        'mailing_method' => ConfigOptions::getValue('mailing_method'),
        'from_name' => AngieApplication::mailer()->getDefaultSender()->getDisplayName(),
        'from_email' => AngieApplication::mailer()->getDefaultSender()->getEmail(),
        'queue_total' => OutgoingMessages::count(), 
        'queue_unsent' => OutgoingMessages::countUnsent(),
        'mailbox_total'=> IncomingMailboxes::count(),
        'mailbox_active' => IncomingMailboxes::countActive(),
        'filter_total' => IncomingMailFilters::count(),
        'filter_active' => IncomingMailFilters::countActive(),
        'conflict_total' => IncomingMails::countConflicts(),
      	'_smarty_function_inline_tabs' => $inline_tabs,
      	'_smarty_function_inline_tabs_id' => HTML::uniqueId('inline_tabs')
      ));
  	} // index
  	
  	/**
  	 * Show email log
  	 */
  	function log() {
  		$per_load = $this->request->get('per_load');
  		if($per_load < 1) {
  			$per_load = 30;
  		} // if
  		
  		if($this->request->get('paged_list')) {
  			$additional_conditions = array();
  			
  			$direction = $this->request->get('direction');
  			if($direction == MailingActivityLog::DIRECTION_IN || $direction == MailingActivityLog::DIRECTION_OUT) {
  				$additional_conditions[] = DB::prepare('direction = ?', $direction);
  			} // if
  			
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

    		$additional_conditions = count($additional_conditions) ? implode(' AND ', $additional_conditions) : null;
    		
    		$this->response->respondWithData(MailingActivityLogs::getSlice($per_load, $exclude, $timestamp, $additional_conditions));
    	} // if
  	} // log
  	
  	/**
  	 * Show details of a specific log entry
  	 */
  	function log_entry() {
  		if($this->request->isAsyncCall()) {
  			$log_entry_id = $this->request->getId('log_entry_id');
  			
  			$log_entry = $log_entry_id ? MailingActivityLogs::findById($log_entry_id) : null;
  			if($log_entry instanceof MailingActivityLog && $log_entry->hasDetails()) {
  			  $this->response->respondWithText($log_entry->renderDetails($this->smarty));
  			} else {
  				$this->response->badRequest();
  			} // if
  		} else {
  		  $this->response->badRequest();
  		} // if 
  	} // log_entry
    
  }