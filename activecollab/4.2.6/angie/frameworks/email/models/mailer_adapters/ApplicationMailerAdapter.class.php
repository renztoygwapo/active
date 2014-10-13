<?php

  /**
   * Base for all application mailer adapters
   * 
   * @package angie.framework.email
   * @subpackage models
   */
  abstract class ApplicationMailerAdapter {
  	
  	/**
  	 * Return mailer name
  	 * 
  	 * @return string
  	 */
  	abstract function getName();
  	
  	/**
  	 * Connect to server, when applicable
  	 */
  	abstract function connect();
  	
  	/**
  	 * Disconnect form server, when application
  	 */
  	abstract function disconnect();
  	
  	/**
  	 * Indicate whether adapter is connected or not
  	 * 
  	 * @return boolean
  	 */
  	abstract function isConnected();
  	
  	/**
  	 * Do send messages
  	 * 
  	 * @param IUser $sender
  	 * @param IUser $recipient
  	 * @param string $subject
  	 * @param string $body
  	 * @param array $attachments
  	 */
    abstract protected function doSend(IUser $sender, IUser $recipient, $subject, $body, $attachments = null);
  	
  	/**
  	 * Log when message is sent
  	 * 
  	 * @var boolean
  	 */
  	protected $log_activity = true;
  	
  	/**
  	 * Send message or messages
  	 * 
  	 * If array of message is provided, they will be sent as a single mail
  	 * 
  	 * @param OutgoingMessage $message
  	 * @param boolean $decorate
  	 */
  	function send($message, $decorate = true) {
  		if(is_foreachable($message)) {
  			foreach($message as $m) {
  				$this->send($m, $decorate);
  			} // if
  		} else {
  			$recipient = $message->getRecipient();
  			$sender = $message->getSender();
  			
  			if(!($sender instanceof IUser)) {
  				$sender = AngieApplication::mailer()->getDefaultSender();
  			} // if
  			
  			// Note: This TRY should not be wrapped up in transaction, because it 
  			// can rollback the parent transaction in case of mailing error
  			
  			try {
          list($subject, $body) = $message->getDecorator()->wrap($message, $decorate);
	  		  $this->doSend($sender, $recipient, $subject, $body, $message->attachments()->get($recipient));

          $message->forceDelete();
	  		  
	  		  // Create a log entry
	  		  if($this->log_activity && $sender instanceof IUser && $recipient instanceof IUser) {
		  		  $log = new MessageSentActivityLog();
		  		  $log->log($sender, $recipient, $subject, $body);
	  		  } // if
	  		} catch(Exception $e) {
	  		  if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
	  		    Logger::log('Failed to send message. Reason: ' . $e->getMessage(), Logger::ERROR);
	  		  } // if
	  		  
	  		  $message->setSendRetries($message->getSendRetries() + 1);
	  		  $message->setLastSendError($e->getMessage());
	  		  $message->save();
	  		} // try
  		} // if
  	} // send
  	
  	/**
  	 * Send digested message based on mutliple messages
  	 * 
  	 * @param array $messages
     * @throws Exception
  	 */
  	function sendDigest($messages) {
  		if(is_foreachable($messages) && count($messages) == 1) {
  		  $this->send($messages[0]);
  		} elseif($messages instanceof OutgoingMessage) {
  			$this->send($messages);
  		} else {
  			$recipient = first($messages)->getRecipient();
	  		$sender = AngieApplication::mailer()->getDefaultSender();
	  		
	  		try {
	  			DB::beginWork('Sending messages @ ' . __CLASS__);

          $message = first($messages);
	  			
	  			list($subject, $body) = $message->getDecorator()->wrap($messages);
	  		
	  		  $this->doSend($sender, $recipient, $subject, $body);
	  		  
	  		  // Clean up messages from queue
	  		  $message_ids = array();
	  		  
  		  	foreach($messages as $message) {
  		  		$message_ids[] = $message->getId();
  		  	} // foreach
	  		  
	  		  DB::execute('DELETE FROM ' . TABLE_PREFIX . 'outgoing_messages WHERE id IN (?)', $message_ids);
	  		  
	  		  // Create a log entry
	  		  if($this->log_activity) {
		  		  $log = new MessageSentActivityLog();
		  		  $log->log($sender, $recipient, $subject, $body);
	  		  } // if
	  		  
	  		  DB::commit('Messages sent @ ' . __CLASS__);
	  		} catch(Exception $e) {
	  			DB::rollback('Messages sent @ ' . __CLASS__);
	  			
	  			throw $e;
	  		} // try
  		} // if
  	} // sendDigest
  	
  }