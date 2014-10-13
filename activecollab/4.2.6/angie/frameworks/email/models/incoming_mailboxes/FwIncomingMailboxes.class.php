<?php

  /**
   * Framework level incoming mailboxes management implementation
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailboxes extends BaseIncomingMailboxes {
    
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
  			return IncomingMailboxes::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return IncomingMailboxes::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice

    /**
     * Test connection and return true if everything's OK
     *
     * This function will return number of unread messages in the mailbox, or FALSE in case of an error. $exception is
     * populated with original exception that mailbox threw
     *
     * @param mixed Exception
     * @return boolean
     */
    static function testConnection($server_address, $server_type, $server_security, $server_port, $mailbox_name, $username, $password, &$exception) {
      try {
        $manager = new PHPImapMailboxManager($server_address, $server_type, $server_security, $server_port, $mailbox_name, $username, $password);
    
        $manager->connect();
        $new_messages = $manager->countUnreadMessages();
        $manager->disconnect();

        return $new_messages;
      } catch(Exception $e) {
        $exception = $e;
        return false;
      } // try
    } // testConnection
    
    /**
     * Find all active mailboxes
     *
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    static function findAllActive($offset = null, $limit = null) {
      return IncomingMailboxes::find(array(
        'conditions' => array('is_enabled > ?', 0),
        'order' => 'name',
        'offset' => $offset,
        'limit' => $limit,
      ));
    } // findAllActive
    
    /**
     * Find mailbox by email field
     *
     * @param string $email
     * @return IncomingMailbox
     */
   static function findByEmail($email) {
      return IncomingMailboxes::find(array(
        'conditions' => array('email = ?', $email),
        'limit' => 1,
        'one' => true,
      ));
    } // findByEmail
    
    /**
     * Find active mailbox by email field
     *
     * @param string $email
     * @return IncomingMailbox
     */
    static function findEnabledByEmail($email) {
      return IncomingMailboxes::find(array(
        'conditions' => array('email = ? AND is_enabled = ?', $email, 1),
        'limit' => 1,
        'one' => true,
      ));
    } // findEnabledByEmail
    
    /**
     * Count active mailboxes
     *
     * @return integer
     */
    static function countActive() {
      return IncomingMailboxes::count(array('is_enabled = ?', 1));
    } // countActive
    
    /**
     * Return names of mailboxes by IDs
     * 
     * @param $ids
     * @return array
     */
    static function listNamesByIds($ids) {
      $mailboxes = self::findByIds($ids);

      if($mailboxes) {
        $names = array();

        foreach($mailboxes as $mailbox) {
          $names[] = $mailbox->getName();
        }//foreach

        return $names;
      } // if

      return null;
    }//listNamesByIds

    // ---------------------------------------------------
    //  Email to Comment Checklist
    // ---------------------------------------------------

    /**
     * Quickly test if reply to comment mailboxe is configured and working
     *
     * @param $from_email
     * @return boolean
     */
    static function testReplyToComments($from_email) {
      if(extension_loaded('imap')) {
        $mailbox = IncomingMailboxes::findByEmail($from_email);

        return $mailbox instanceof IncomingMailbox && $mailbox->getIsEnabled() && $mailbox->getLastStatus() != IncomingMailbox::LAST_CONNECTION_STATUS_ERROR;
      } else {
        return false;
      } // if
    } // testReplyToComments
    
  }