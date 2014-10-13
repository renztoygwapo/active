<?php

  /**
   * Incoming mail activity log
   * 
   * @package angie.framework.email
   * @subpackage models
   */
  abstract class IncomingMailingActivityLog extends MailingActivityLog {
  	
  	/**
  	 * Log activity and save it to database
  	 * 
  	 * @param IUser $from
  	 * @param IUser $to
  	 * @param array $properties
  	 * @param boolean $save
  	 */
  	function log(IUser $from, IUser $to, $properties = null, $save = true) {
  		$this->setDirection(self::DIRECTION_IN);
  		if(!$from instanceof IUser) {
  		  $from = new AnonymousUser('Unknown from email address' , INCOMING_MAIL_INVALID_EMAIL_ADDRESS);
  		}//if
  		if(!$to instanceof IUser) {
  		  $to = new AnonymousUser('Unknown to email address' , INCOMING_MAIL_INVALID_EMAIL_ADDRESS);
  		}//if
  		
  		parent::log($from, $to, $properties, $save);
  	} // log
  	
  	/**
     * Returns Mailbox which is associated with object
     *
     * @param void
     * @return IncomingMailbox
     */
    function getMailbox() {
      return IncomingMailboxes::findById($this->getAdditionalProperty('mailbox_id'));
    } // getMailbox
    
    /**
     * Return applied action
     * 
     * @return Object
     */
    function getAppliedAction() {
      $action_class = $this->getAdditionalProperty('action_name');
      return new $action_class();
    }//getAppliedAction
    
    /**
     * Get applied Filter
     * 
     * @return IncomingMailFilter
     */
    function getAppliedFilter() { 
      return IncomingMailFilters::findById($this->getAdditionalProperty('filter_id'));
    } //getAppliedFilter
    
    /**
     * Return mailbox name
     *
     * @return string
     */
    function getMailboxDisplayName() {
      $mailbox = $this->getMailbox();
      if ($mailbox instanceof IncomingMailbox) {
        return $mailbox->getDisplayName();
      } // if
      return lang('Unknown');
    } // getMailboxName
    
    /**
     * Get incoming mail
     * 
     * @return IncomingMail
     */
    function getIncomingMail() {
      return IncomingMails::findById($this->getAdditionalProperty('incoming_email_id'));
    } // getIncomingMail
    
    /**
     * Return mailbox view URL
     *
     * @return string
     */
    function getMailboxViewUrl() {
      $mailbox = $this->getMailbox();
      if ($mailbox instanceof IncomingMailbox) {
        return $this->mailbox->getViewUrl();
      } // if
      return lang('Unknown');
    } // getMailboxViewUrl
    
    /**
     * Return created object
     */
    function getCreatedObject() {
      $created_object_class = $this->getAdditionalProperty('target_type');
      
      if(empty($created_object_class)) {
        return null;
      } else {
        return new $created_object_class($this->getAdditionalProperty('target_id'));
      } // if
    } // getCreatedObject
    
    /**
     * Get view url for resulting object
     *
     * @return string
     */
    function getResultingObjectUrl() {
      $resulting_object = $this->getCreatedObject();
      if(!$resulting_object) {
        return false;
      }//if
      
      if($resulting_object instanceof Comment) {
        return $resulting_object->getRealViewUrl();
      } else {
        return $resulting_object->getViewUrl();
      } //if
    } // getResultingObjectUrl
  	
  }