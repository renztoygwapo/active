<?php

  /**
   * Framework level incoming email messages manager implementation
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMails extends BaseIncomingMails {
    
    /**
  	 * Return slice of incoming mailbox definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return IncomingMails::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} else {
  			return IncomingMails::find(array(
  			  'order' => 'id', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
    
    /**
     * Count conflicts emails
     *
     * @return integer
     */
    static function countConflicts() {
      return IncomingMails::count();
    } // countPending
    
    /**
     * Find Conflict mails
     */
    static function findConflicts() {
      return IncomingMails::find();
    } // findConflicts

    /**
     * Delete conflict by ids
     * 
     * @param array $ids
     * @return mixed
     */
    static function deleteByIds($ids) {
      IncomingMailAttachments::deleteByMailIds($ids);
      return self::delete(array('id IN (?)', $ids));
    }//deleteByIds
    
    /**
     * Delete all conflicts
     *
     * @return mixed
     */
    static function deleteAll() {
      IncomingMailAttachments::deleteAll();
      return self::delete();
    }//deleteByIds
  }
