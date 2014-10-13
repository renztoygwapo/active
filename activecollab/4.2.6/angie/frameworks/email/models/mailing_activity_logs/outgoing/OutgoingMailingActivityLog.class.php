<?php

  /**
   * Outgoing mail activity log
   * 
   * @package angie.framework.email
   * @subpackage models
   */
  abstract class OutgoingMailingActivityLog extends MailingActivityLog {
  	
  	/**
  	 * Log activity and save it to database
  	 * 
  	 * @param IUser $from
  	 * @param IUser $to
  	 * @param array $properties
  	 * @param boolean $save
  	 */
  	function log(IUser $from, IUser $to, $properties = null, $save = true) {
  		$this->setDirection(self::DIRECTION_OUT);
  		
  		parent::log($from, $to, $properties, $save);
  	} // log
  	
  }