<?php

  // Build on top of outgoing email admin controller
  AngieApplication::useController('outgoing_email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Outgoing email queue management controller
   *
   * @package angie.frameworks.email
   * @subpackage controllers
   */
  abstract class FwOutgoingMessagesAdminController extends OutgoingEmailAdminController {
    
    /**
     * Loaded outgoing message
     *
     * @var OutgoingMessage
     */
    protected $active_outgoing_message;
    
    /**
     * Attachments controller delegate
     *
     * @var AttachmentsController
     */
    protected $attachments_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'outgoing_messages_admin') {
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'outgoing_messages_admin_message');
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $message_id = $this->request->getId('message_id');
      if($message_id) {
      	$this->active_outgoing_message = OutgoingMessages::findById($message_id);
      } // if
      
      if($this->active_outgoing_message instanceof OutgoingMessage) {
      	$this->wireframe->breadcrumbs->add('outgoing_messages_admin', $this->active_outgoing_message->getSubject(), $this->active_outgoing_message->getViewUrl());
      } else {
      	$this->active_outgoing_message = new OutgoingMessage();
      } // if
      
      $this->smarty->assign('active_outgoing_message', $this->active_outgoing_message);
      
      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_outgoing_message,
        ));
      } // if
    } // __construct
    
    /**
     * Display messages that are in email queue
     */
    function index() {
    	$messages_per_page = 15;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(OutgoingMessages::getSlice($messages_per_page, $exclude, $timestamp));
    	} else {
    		$this->smarty->assign(array(
    		  'messages' => OutgoingMessages::getSlice($messages_per_page), 
    			'messages_per_page' => $messages_per_page, 
    		  'total_messages' => OutgoingMessages::count(), 
    		));
    	} // if
    } // index
    
    /**
     * Show message details
     */
    function view() {
    	if($this->active_outgoing_message->isNew()) {
    		$this->response->notFound();
    	} // if
    } // view

    /**
     * Show raw message body
     */
    function view_raw_body() {
      if($this->active_outgoing_message->isLoaded()) {
        $decorated = $this->active_outgoing_message->getDecorator()->wrap($this->active_outgoing_message);

        $this->response->respondWithContent($decorated[1]);
      } else {
        $this->response->notFound();
      } // if
    } // view_raw_body
    
    /**
     * Send a single message
     */
    function send() {
      if($this->active_outgoing_message->isNew()) {
    		$this->response->notFound();
    	} // if
      
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        try {
          $send_retries = $this->active_outgoing_message->getSendRetries();

          $this->active_outgoing_message->send();

          if($this->active_outgoing_message->isLoaded() && $this->active_outgoing_message->getSendRetries() > $send_retries) {
            $this->response->respondWithData($this->active_outgoing_message, array(
              'as' => 'message'
            ));
          } else {
            $this->response->ok();
          } // if
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // send
    
    /**
     * Delete a specific message from a queue
     */
    function delete() {
      if($this->active_outgoing_message->isNew()) {
    		$this->response->notFound();
    	} // if
      
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        try {
        	$this->active_outgoing_message->delete();
          $this->response->ok();
        } catch(Exception $e) {
        	$this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }