<?php

  class IncomingMessageDeleteActivityLog extends IncomingMailingActivityLog {
    
    /**
  	 * Return name of this log entry
  	 * 
  	 * @return string
  	 */
  	function getName() {
  	  return lang('Incoming mail ":subject" deleted.', array('subject' => $this->getAdditionalProperty('subject')));
  	} // getName
  	
  	
  	/**
  	 * Log activity and save it to database
  	 * 
  	 * @param IncomingMailbox $mailbox
  	 * @param string $error_message
  	 * @param boolean $save
  	 */
  	function log(IncomingMailbox $mailbox = null, IncomingMail $incoming_email = null, $save = true) {
  		
  		parent::log(
  		  $incoming_email instanceof IncomingMail ? $incoming_email->getCreatedBy() : null, 
  	      $incoming_email instanceof IncomingMail ? $incoming_email->getToUser() : null, 
  	      array(
    		  'mailbox_name' => $mailbox instanceof IncomingMailbox ? $mailbox->getName() : null,
  	          'mailbox_id' => $mailbox instanceof IncomingMailbox ? $mailbox->getId() : null,
    		  'subject' => $incoming_email instanceof IncomingMail ? $incoming_email->getSubject() : null,
    	      'body' =>  $incoming_email instanceof IncomingMail ? $incoming_email->getBody() : null,
  		  ), $save);
  	} // log
  	
  	/**
  	 * We have details to show to the user about this particular mailing log
  	 * 
  	 * @return boolean
  	 */
  	function hasDetails() {
  		return false;
  	} // hasDetails
  }