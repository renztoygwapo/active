<?php

  /**
   * Incoming mail message received log entry
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class IncomingMessageReceivedActivityLog extends IncomingMailingActivityLog {

    /**
  	 * Return name of this log entry
  	 * 
  	 * @return string
  	 */
  	function getName() {
  	  return lang('Message ":subject" received and imported', array('subject' => $this->getAdditionalProperty('subject')));
  	} // getName

    /**
     * Log entry into the database
     *
     * @param null $mailbox
     * @param null $action
     * @param null $incoming_email
     * @param null $filter
     * @param null $target
     * @param bool $save
     */
    function log($mailbox = null, $action = null, $incoming_email = null, $filter = null, $target = null, $save = true) {
  	  parent::log(
  	    $incoming_email instanceof IncomingMail ? $incoming_email->getCreatedBy() : null, 
  	    $incoming_email instanceof IncomingMail ? $incoming_email->getToUser() : null,
  	    array(
    		'filter_id' => $filter instanceof IncomingMailFilter ? $filter->getId() : null, 
        'filter_name' => $filter instanceof IncomingMailFilter ? $filter->getName() : null,
        'action_description' => $action instanceof IncomingMailAction ? $action->getDescription() : null,

        'action_name' => $action ? $action->getName() : null,

    		'target_type' => $target instanceof ApplicationObject ? get_class($target) : null, 
    		'target_id' => $target instanceof ApplicationObject ? $target->getId() : null,
  	    'mailbox_id' => $mailbox instanceof IncomingMailbox ? $mailbox->getId() : null,
  	    'incoming_email_id' => $incoming_email instanceof IncomingMail ? $incoming_email->getId() : null,
  	    'subject' => $incoming_email instanceof IncomingMail ? $incoming_email->getSubject() : null,
  	    'body' =>  $incoming_email instanceof IncomingMail ? $incoming_email->getBody() : null,
  	    'from_email_original' => $incoming_email instanceof IncomingMail ? $incoming_email->getOriginalFromEmail() : null,
  	    ), 
  	    $save);
  	} // log
  	
  		/**
  	 * We have details to show to the user about this particular mailing log
  	 * 
  	 * @return boolean
  	 */
  	function hasDetails() {
  		return true;
  	} // hasDetails
  	
  	/**
  	 * Render log entry details for view in flyout
  	 * 
  	 * @param Smarty $smarty
  	 * @return string
  	 */
  	function renderDetails(Smarty $smarty) {
  		return parent::renderDetails($smarty, get_view_path('incoming_received', 'activity_log_details', EMAIL_FRAMEWORK));
  	} // renderDetails
  	
  	 /**
     * Return log entry icon URL
     * 
     * @return string
     */
    function getIconUrl() {
    	return AngieApplication::getImageUrl('icons/16x16/mail-incoming.png', ENVIRONMENT_FRAMEWORK);
    } // getIconUrl
    
    
  	  	
  }