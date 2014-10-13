<?php

  /**
   * Framework level incoming mail attachments manager
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailFilters extends BaseIncomingMailFilters {
    
     /**
  	 * Return slice of incoming mailbox definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return IncomingMailFilters::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'position', 
  			  'limit' => $num,  
  			));
  		} else {
  			return IncomingMailFilters::find(array(
  			  'order' => 'position', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
    
    /**
     * Return all active filters
     *
     * @return DBResult
     */
    static function findAllActive() {
      return IncomingMailFilters::find(array(
        'conditions' => array('is_enabled > ?',0),
        'order' => 'position',
      ));
    }//findAllActive
    
    /**
     * Count active filters
     *
     * @return integer
     */
    static function countActive() {
      return IncomingMailFilters::count(array('is_enabled = ?',1));
    }//countActive

  }