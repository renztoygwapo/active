<?php

  class IncomingMessageServerErrorActivityLog extends IncomingMailingActivityLog {
    
    /**
  	 * Return name of this log entry
  	 * 
  	 * @return string
  	 */
  	function getName() {
  	  return lang("Couldn't get message from server - :mailbox_name. Error message: :error_message.", array('mailbox_name' => $this->getAdditionalProperty('mailbox_name'),'error_message'=>$this->getAdditionalProperty('error_message')));
  	} // getName
  	
  	
  	/**
  	 * Log activity and save it to database
  	 * 
  	 * @param IncomingMailbox $mailbox
  	 * @param string $error_message
  	 * @param boolean $save
  	 */
  	function log(IncomingMailbox $mailbox = null, $error_message = null, $save = true) {
  		
  		parent::log(null, null, array(
  		  'mailbox_name' => $mailbox instanceof IncomingMailbox ? $mailbox->getName() : 'CLI',
  		  'error_message' => $error_message
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
  	
  	 /**
     * Return log entry icon URL
     * 
     * @return string
     */
    function getIconUrl() {
    	return AngieApplication::getImageUrl('icons/16x16/mail-info.png', ENVIRONMENT_FRAMEWORK);
    } // getIconUrl
  	
  }