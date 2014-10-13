<?php

  /**
   * Outgoing messages manager
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwOutgoingMessages extends BaseOutgoingMessages {
  	
  	/**
  	 * Return messages by mailing method
  	 * 
  	 * Note: this method will skip messages that reached max number of send 
  	 * retries without being sent
  	 * 
  	 * @param string $method
  	 * @param integer $limit
  	 * @return DBResult
  	 */
  	static function findByMethod($method, $limit = null) {
  		return OutgoingMessages::find(array(
  		  'conditions' => array('mailing_method = ? AND send_retries < ?', $method, MAILING_QUEUE_MAX_SEND_RETRIES), 
  			'order' => 'created_on',
  		  'limit' => $limit,  
  		));
  	} // findByMethod
  	
  	/**
  	 * Return slice of outgoing messages based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		$max_date = $timestamp ? new DateTimeValue($timestamp) : new DateTimeValue();
  		
  		if($exclude) {
  			return self::find(array(
  			  'conditions' => array('id NOT IN (?) AND created_on <= ?', $exclude, $max_date), 
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} else {
  			return self::find(array( 
  				'conditions' => array('created_on <= ?', $max_date),
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  	/**
  	 * Return number of items that are not sent because max retries was reached
  	 * 
  	 * @return integer
  	 */
  	static function countUnsent() {
  		return OutgoingMessages::count(array('send_retries >= ?', MAILING_QUEUE_MAX_SEND_RETRIES));
  	} // countUnsent
  	
  }