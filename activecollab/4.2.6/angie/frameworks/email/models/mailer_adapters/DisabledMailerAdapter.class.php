<?php

  /**
   * Disabled mailer adapter
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class DisabledMailerAdapter extends ApplicationMailerAdapter {
  	
  	/**
  	 * Return mailer name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return lang('Disabled');
  	} // getName

  	/**
  	 * Connect to server, when applicable
  	 */
  	function connect() {
  		
  	} // connect
  	
  	/**
  	 * Disconnect form server, when application
  	 */
  	function disconnect() {
  		
  	} // disconnect
  	
  	/**
  	 * Indicate whether adapter is connected or not
  	 * 
  	 * @return boolean
  	 */
  	function isConnected() {
  		return true;
  	} // isConnected
  	
  	/**
  	 * Send the message... Nowhere
  	 * 
  	 * @param IUser $sender
  	 * @param IUser $recipient
  	 * @param string $subject
  	 * @param string $body
  	 * @param array $attachments
  	 */
  	protected function doSend(IUser $sender, IUser $recipient, $subject, $body, $attachments = null) {
  		// Black hole
  	} // doSend
  	
  	/**
  	 * Log when message is sent
  	 * 
  	 * @var boolean
  	 */
  	protected $log_activity = false;
  	
  }