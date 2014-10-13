<?php

  /**
   * Silent mailer adapter
   * 
   * Different between silend and disabled adapter is that disabled does not log 
   * any activity, while silend will do everything regular adapter would do 
   * except actually sending an email
   * 
   * @package angie.frameworks.email
   * @subpackage model
   */
  class SilentMailerAdapter extends ApplicationMailerAdapter {
  	
  	/**
  	 * Return mailer name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return lang('Silent');  		
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
  		// Don't send an email, this is silent adapter :)
  	} // doSend
  	
  }