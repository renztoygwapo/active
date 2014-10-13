<?php

  /**
   * Framework level mailing activity logs manager
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwMailingActivityLogs extends BaseMailingActivityLogs {

  	/**
  	 * Return log slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @param mixed $additional_conditions
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null, $additional_conditions = null) {
  		$max_date = $timestamp ? new DateTimeValue($timestamp) : new DateTimeValue();
  		
  		if($additional_conditions) {
  			$additional_conditions = " AND $additional_conditions";
  		} // if
  		
  		if($exclude) {
  			return MailingActivityLogs::find(array(
  			  'conditions' => array("id NOT IN (?) AND created_on <= ? $additional_conditions", $exclude, $max_date), 
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} else {
  			return MailingActivityLogs::find(array( 
  				'conditions' => array("created_on <= ? $additional_conditions", $max_date),
  			  'order' => 'created_on DESC', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice

    /**
     * Clean up mailing activity log
     */
    static function cleanUp() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'mailing_activity_logs WHERE created_on < ?', DateValue::makeFromString('-30 days'));
    } // cleanUp
  	
  }