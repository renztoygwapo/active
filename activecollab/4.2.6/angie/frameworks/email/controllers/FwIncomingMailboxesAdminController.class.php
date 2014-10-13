<?php

  // Inherit application level controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level mailbox management controller
   *
   * @package angie.frameworks.email
   * @subpackage controllers
   */
  abstract class FwIncomingMailboxesAdminController extends EmailAdminController {
    
    /**
     * Active Mailbox
     * 
     * @var IncomingMailbox
     */
    protected $active_mailbox;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(AngieApplication::isOnDemand()) {
        $this->response->notFound();
      } //if

      require_once ANGIE_PATH . '/classes/mailboxmanager/init.php';
      
      $this->wireframe->breadcrumbs->add('incoming_mailboxes', lang('Incoming Mail'), Router::assemble('incoming_email_admin_mailboxes'));
     
      $mailbox_id = $this->request->getId('mailbox_id');
      if($mailbox_id) {
        $this->active_mailbox = IncomingMailboxes::findById($mailbox_id);
      } // if
      
      if($this->active_mailbox instanceof IncomingMailbox) {
        $this->wireframe->breadcrumbs->add('mailbox', $this->active_mailbox->getHost(), $this->active_mailbox->getViewUrl());
      } else {
        $this->active_mailbox = new IncomingMailbox();
      } // if
      
      $this->smarty->assign(array(
        'active_mailbox' => $this->active_mailbox,
      ));
    } // __construct
    
    /**
     * Mailbox index
     */
    function index() {
      $this->wireframe->actions->add('new_incoming_mailbox', lang('New Mailbox'), Router::assemble('incoming_email_admin_mailbox_add'), array(
        'onclick' => new FlyoutFormCallback('incoming_mailbox_created'), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
      
      $mailboxes_per_page = 10;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(IncomingMailboxes::getSlice($mailboxes_per_page, $exclude, $timestamp));
    	} else {
    		$this->smarty->assign(array(
    		  'mailboxes' => IncomingMailboxes::getSlice($mailboxes_per_page), 
    			'mailboxes_per_page' => $mailboxes_per_page, 
    		  'total_mailboxes' => IncomingMailboxes::count(), 
    		));
    	} // if
    }//index
    
    /**
  	 * Show mailbox log
  	 */
  	function view() {
  		if($this->active_mailbox->isNew()) {
  		  $this->response->notFound();
  		} // if
  	} // view
       
    /**
     * List messages on server
     */
    function list_messages() {
      $manager = $this->active_mailbox->getMailboxManager();
      $this->wireframe->breadcrumbs->add('incoming_mailbox', clean($this->active_mailbox->getEmail()), $this->active_mailbox->getViewUrl());

      try {
        $connection = $manager->connect();
        $total_emails = $manager->countMessages();
        $headers = $manager->listMessagesHeaders(1, $total_emails);

        $this->smarty->assign(array(
          'connection'    => $connection,
          'unread_emails' => $manager->countUnreadMessages(),
          'total_emails'  => $total_emails,
          'headers'       => $headers,
        ));

      } catch (Exception $e) {
        $this->response->exception($e->getMessage());
      } // try
    } // list_messages
    
    /**
     * Delete emails from mailbox
     * 
     */
    function delete_message_from_server() {
      $manager = $this->active_mailbox->getMailboxManager();
      $connection = $manager->connect();
      if($this->request->isSubmitted()) {
        try {
          $delete_emails = $this->request->post('delete_emails');
          if(is_foreachable($delete_emails)) {
            foreach ($delete_emails as $delete_id) {
              $manager->deleteMessage($delete_id,true);
            }//foreach
          }//if
          
          $this->flash->success('Selected email deleted.');
          
          $this->response->redirectToUrl($this->active_mailbox->getListEmailsUrl());
        } catch (Exception $e) {
          $this->response->exception($e);
        }//try
      } else {
        $this->response->badRequest();
      } // if
      die();
    }//delete_message_from_server
    
    
    
    /**
     * Form for adding mailbox
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $mailbox_data = $this->request->post('mailbox', array(
          'mailbox' => 'INBOX',
          'email' => $this->request->get('default_email_address'),
          'server_type' => MM_SERVER_TYPE_POP3,
          'port' => 110,
        ));
        
        $this->smarty->assign('mailbox_data', $mailbox_data);
        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Add new mailbox @ ' . __CLASS__); 
            
            $this->active_mailbox->setAttributes($mailbox_data);
            $this->active_mailbox->setIsEnabled(false);
                    
            $this->active_mailbox->save();
            
            DB::commit('New mailbox added @ ' . __CLASS__);
            // If we can connect, enable the mailbox
            
            $this->active_mailbox->testAndEnable();
            
            $this->response->respondWithData($this->active_mailbox, array(
              'as' => 'incoming_mailbox', 
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to add new mailbox @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Page for editing mailbox
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if($this->active_mailbox->isNew()) {
          $this->response->notFound();
        } // if
         
        $this->wireframe->breadcrumbs->add('incoming_mailbox', clean($this->active_mailbox->getDisplayName()), $this->active_mailbox->getViewUrl());
        
        $mailbox_data = $this->request->post('mailbox');
        if (!is_array($mailbox_data)) {
          $mailbox_data = array(
            'name' => $this->active_mailbox->getName(),
            'email' => $this->active_mailbox->getEmail(),
            'host' => $this->active_mailbox->getHost(),
            'username' => $this->active_mailbox->getUsername(),
            'password' => $this->active_mailbox->getPassword(),
            'server_type' => $this->active_mailbox->getServerType(),
            'security' => $this->active_mailbox->getSecurity(),
            'port' => $this->active_mailbox->getPort(),
            'mailbox' => $this->active_mailbox->getMailbox(),
            'is_enabled' => $this->active_mailbox->getIsEnabled(),
          );
        } // if
        
        $this->smarty->assign("mailbox_data", $mailbox_data);
              
        if ($this->request->isSubmitted()) {
          try {
            DB::beginWork('Updating mailbox @ ' . __CLASS__);
            
            $this->active_mailbox->setAttributes($mailbox_data);
            $this->active_mailbox->save();
            
            DB::commit('Mailbox updated @ ' . __CLASS__);
            
            $this->response->respondWithData($this->active_mailbox, array(
              'as' => 'incoming_mailbox', 
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to update mailbox @ ' . __CLASS__);
            $this->response->exception($e);
          } // try     
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit
    
    /**
     * Action for deleting mailbox
     */
    function delete() {
      if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if ($this->active_mailbox->isNew()) {
          $this->response->notFound();
        } // if
        
        try {
          $this->active_mailbox->delete();
          $this->response->respondWithData($this->active_mailbox, array(
            'as' => 'incoming_mailbox', 
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try 
      } //if
    } // delete
    
    /**
     * Enable mailbox
     */
    function enable() {
      if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
          if($this->active_mailbox->isNew()) {
            $this->response->notFound();
          } //if
          
          try {
            DB::beginWork('Enabling mailbox @ ' . __CLASS__);
            
            //do not allow if we want to enable mailbox but could not connect to email server 
            $this->active_mailbox->getMailboxManager()->testConnection();
            
            $this->active_mailbox->setIsEnabled(true);
            $this->active_mailbox->setFailureAttempts(0);
         
            $this->active_mailbox->save();
            
            DB::commit('Mailbox enabled @ ' . __CLASS__);
            
            $this->response->respondWithData($this->active_mailbox, array(
              'as' => 'incoming_mailbox', 
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to enable mailbox @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
      } else {
        $this->response->badRequest();
      } // if
    }//enable
    
    /**
     * Disable mailbox
     */
    function disable() {
      if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
          if(!$this->active_mailbox instanceof IncomingMailbox) {
            $this->httpError(HTTP_ERR_NOT_FOUND, lang("Can't find mailbox."), true, true);
          } //if
          
          try {
            DB::beginWork('Disabling mailbox @ ' . __CLASS__);
                        
            $this->active_mailbox->setIsEnabled(false);
            $this->active_mailbox->save();
            
            DB::commit('Mailbox disabled @ ' . __CLASS__);
            
            $this->response->respondWithData($this->active_mailbox, array(
              'as' => 'incoming_mailbox', 
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to disable mailbox @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
      } else {
        $this->response->badRequest();
      } // if
    }//disable
    
   
    /**
     * Controller action called via ajax to check if user has provided valid 
     * mail server connection data
     */
    function test_mailbox_connection() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
       
        if(!extension_loaded('imap')) {
          $this->response->operationFailed(array(
            'message' => lang('IMAP extension is not loaded'), 
          ));
        } // if

        $mailbox = array_var($_POST, 'mailbox', array());
        $connection_error = null;

        $new_messages = IncomingMailboxes::testConnection($mailbox['host'], $mailbox['server_type'], $mailbox['security'], $mailbox['port'], $mailbox['mailbox'], $mailbox['username'], $mailbox['password'], $connection_error);

        if($connection_error instanceof Exception) {
          $this->response->exception($connection_error);
        } else {
          $this->response->respondWithData($new_messages);
        } // if
       } else {
        $this->response->badRequest();
      } // if
    } // test_mailbox_connection
    
  }