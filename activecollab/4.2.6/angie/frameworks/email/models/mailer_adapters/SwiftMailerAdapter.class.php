<?php

  // Make sure that we have SwiftMailer included
  SwiftMailerForAngie::includeSwiftMailer();

  /**
   * Swift mailer adapter
   * 
   * @package angie.frameworks.email
   * @subpackage model
   */
  abstract class SwiftMailerAdapter extends ApplicationMailerAdapter {
  	
  	/**
		 * Send message
		 * 
		 * @param IUser $sender
		 * @param IUser $recipient
		 * @param string $subject
		 * @param string $body
		 * @param array $attachments
     * @return integer
		 */
    protected function doSend(IUser $sender, IUser $recipient, $subject, $body, $attachments = null) {
			if(!$this->isConnected()) {
			  $this->connect();
			} // if
			
			$message = Swift_Message::newInstance($subject);
			
			$message->setEncoder($this->getEncoder());
			$message->setCharset('utf-8');
			
			$message->setBody($body, 'text/html');

      // Provide plain text version of the body
      $plain_text = HTML::toPlainText($body);

      if($plain_text) {
        $message->addPart($plain_text, 'text/plain');
      } // if
			
			if(AngieApplication::mailer()->getMarkAsBulk()) {
			  $message->getHeaders()->addTextHeader('Auto-Submitted', 'auto-generated');
			  $message->getHeaders()->addTextHeader('Precedence', 'bulk');
			} // if
			
			if(AngieApplication::mailer()->getForceMessageFrom()) {
			  $message->setFrom($this->userToArray(AngieApplication::mailer()->getDefaultSender()));
			} else {
			  $message->setFrom($this->userToArray($sender));
			} // if
			
			$message->setTo($this->userToArray($recipient));
			$message->setReturnPath(AngieApplication::mailer()->getDefaultSender()->getEmail());
			$message->setReplyTo(AngieApplication::mailer()->getDefaultSender()->getEmail());
			
			if($attachments) {
			  foreach($attachments as $attachment) {
			    if($attachment instanceof Attachment) {
			      $message->attach(Swift_Attachment::fromPath($attachment->getFilePath(), $attachment->getMimeType())->setFilename($attachment->getName()));
			    } // if
			  } // foreach
			} // if
      
      return $this->mailer->send($message);
		} // doSend
		
		/**
		 * Mailer instance
		 *
		 * @var Swift_Mailer
		 */
		private $mailer;
		
		/**
		 * Assume that we have a connection (can't check)
		 */
		function isConnected() {
			return $this->mailer instanceof Swift_Mailer && $this->mailer->getTransport() instanceof Swift_Transport;
		} // isConnected
		
		/**
		 * Can't connect to the server, handled by PHP
		 */
		function connect() {
		  if($this->isConnected()) {
		    return;
		  } // if
			
			$transport = $this->getTransport();
			
			if($transport instanceof Swift_Transport) {
			  $this->mailer = Swift_Mailer::newInstance($transport);
			  $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin(new SwiftMailerForLogger()));
			} // if
		} // connect
	
		/**
		 * Can't disconnect from the server, handled by PHP
		 */
		function disconnect() {
			if($this->mailer instanceof Swift_Mailer && $this->mailer->getTransport() instanceof Swift_Transport && $this->mailer->getTransport()->isStarted()) {
			  $this->mailer->getTransport()->stop();
			} // if
		} // disconnect
		
		// ---------------------------------------------------
		//  Utils
		// ---------------------------------------------------
		
		/**
		 * Return user as array, useful for message builder
		 * 
		 * @param IUser $user
		 * @return array
		 */
		protected function userToArray(IUser $user) {
		  return $user->getName() ? array($user->getEmail() => $user->getName()) : array($user->getEmail());
		} // userToArray
		
		/**
		 * Cached encoder instance
		 *
		 * @var Swift_Encoder
		 */
		private $encoder = false;
		
		/**
		 * Return encoder instance
		 * 
		 * @return Swift_Mime_ContentEncoder
		 */
		function getEncoder() {
		  if($this->encoder === false) {
		    $this->encoder = Swift_Encoding::get8BitEncoding();
		  } // if
		  
		  return $this->encoder;
		} // getEncoder
		
		/**
		 * Return mailer transport that we'll use to send messages
		 * 
		 * @return Swift_Transport
		 */
		abstract function getTransport();
  	
  }