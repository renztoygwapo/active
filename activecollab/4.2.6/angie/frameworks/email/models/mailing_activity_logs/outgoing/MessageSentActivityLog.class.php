<?php

  /**
   * Log that message has been successfully sent
   *
   * @package angie.frameworks.mailing
   * @subpackage models
   */
  class MessageSentActivityLog extends OutgoingMailingActivityLog {
  	
  	/**
  	 * Return log entry name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return lang('":subject" sent', array('subject' => $this->getAdditionalProperty('subject', '[No Subject]')));
  	} // getName
  	
  	/**
  	 * Log message sent activity
  	 * 
  	 * @param IUser $from
  	 * @param IUser $to
  	 * @param string $subject
  	 * @param string $body
  	 * @param boolean $save
  	 */
  	function log(IUser $from, IUser $to, $subject, $body, $save = true) {
  		parent::log($from, $to, array(
  		  'subject' => $subject, 
  		  'body' => $body, 
  		), $save);
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
  		return parent::renderDetails($smarty, get_view_path('message_sent', 'activity_log_details', EMAIL_FRAMEWORK));
  	} // renderDetails
  	
  	 /**
     * Return log entry icon URL
     * 
     * @return string
     */
    function getIconUrl() {
    	return AngieApplication::getImageUrl('icons/16x16/mail-outgoing.png', ENVIRONMENT_FRAMEWORK);
    } // getIconUrl
  	
  }