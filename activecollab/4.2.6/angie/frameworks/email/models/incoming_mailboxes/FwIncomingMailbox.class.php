<?php

  /**
   * Framework level incoming mailbox implementation
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailbox extends BaseIncomingMailbox implements IRoutingContext {
    
    // Enabled
    const MAILBOX_ENABLED = 1;
    const MAILBOX_DISABLED = 0;

    // Last connection status
    const LAST_CONNECTION_STATUS_NOT_CHECKED = 0;
    const LAST_CONNECTION_STATUS_OK = 1;
    const LAST_CONNECTION_STATUS_ERROR = 2;
    
    /**
     * Returns manager object
     * 
     * @return PHPImapMailboxManager
     */
    function getMailboxManager () {
      return new PHPImapMailboxManager(
        $this->getHost(),
        $this->getServerType(),
        $this->getSecurity(),
        $this->getPort(),
        $this->getMailbox(),
        $this->getUsername(),
        $this->getPassword()
      );
    } // getMailboxManager
    
    /**
     * Increment failure attempts to this mailbox on connection failure
     */
    function incrementFailureAttemps() { 
      $this->setFailureAttempts($this->getFailureAttempts() + 1);
    } //incrementFailureAttemp
    
    /**
     * Get mailbox display name
     *
     * @return string
     */
    function getDisplayName() {
      return $this->getName() ? $this->getName() : $this->getUsername() . '@' . $this->getHost();
    } // getDisplayName
    
    /**
     * Returns string dependable of last status check
     *
     * @return string
     */
    function getFormattedLastStatus() {
      switch ($this->getLastStatus()) { 
        case IncomingMailbox::LAST_CONNECTION_STATUS_NOT_CHECKED:
          return lang('Not Checked');         
        case IncomingMailbox::LAST_CONNECTION_STATUS_OK:
          return lang('Status OK');
        case IncomingMailbox::LAST_CONNECTION_STATUS_ERROR:
          return lang('Last update failed');
        default:
          return lang('Not Checked');
      } // switch
    } // getFormattedLastStatus
    
    /**
     * Test connection and enable on success
     */
    function testAndEnable() {
      $this->getMailboxManager()->testConnection();
      $this->setIsEnabled(true);
      $this->save();
    } // testAndEnable
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['email'] = $this->getEmail();
      $result['mailbox'] = $this->getMailbox();
      $result['username'] = $this->getUsername();
      $result['password'] = $this->getPassword();
      $result['host'] = $this->getHost();
      $result['port'] = $this->getPort();
      $result['server_type'] = $this->getServerType();
      $result['security'] = $this->getSecurity();
      $result['status'] = $this->getLastStatus();
      $result['is_enabled'] = $this->getIsEnabled();
      
      $result['urls']['list_messages'] = $this->getListEmailsUrl();
      $result['urls']['enable'] = $this->getEnableUrl();
      $result['urls']['disable'] = $this->getDisableUrl();
      
      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'incoming_email_admin_mailbox';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('mailbox_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
     /**
     * Return add Mailbox URL
     *
     * @return string
     */
    function getAddUrl() {
      return Router::assemble('incoming_email_admin_mailbox_add');
    } // getAddUrl
    
    /**
     * Return list emails Mailbox URL
     *
     * @return string
     */
    function getListEmailsUrl() {
      return Router::assemble('incoming_email_admin_mailbox_list_messages', array(
        'mailbox_id' => $this->getId()
      ));
    } // getListEmailsUrl
    
    
    /**
     * Return delete messages Mailbox URL
     *
     * @return string
     */
    function getDeleteMessagesUrl() {
      return Router::assemble('incoming_email_admin_mailbox_delete_messages', array(
        'mailbox_id' => $this->getId()
      ));
    } // getDeletemessagesUrl
    
   
    /**
     * Return enable mailbox URL
     * 
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('incoming_email_admin_mailbox_enable', array(
        'mailbox_id' => $this->getId()
      ));
    } // getEnableUrl
    
    /**
     * Return enable mailbox URL
     * 
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('incoming_email_admin_mailbox_disable', array(
        'mailbox_id' => $this->getId()
      ));
    } // getDisableUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Function to validate class
     *
     * @param ValidationErrors $errors
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('mailbox')) {
        $errors->addError(lang('Mailbox name is required'), 'mailbox');
      } // if
      
      if(!$this->validatePresenceOf('username')) {
        $errors->addError(lang('Username is required'), 'username');
      } // if
      
      if(!$this->validatePresenceOf('password')) {
        $errors->addError(lang('Password is required'), 'password');
      } // if
      
      if(!$this->validatePresenceOf('host')) {
        $errors->addError(lang('Hostname is required'), 'host');
      } // if
      
      if(!$this->validatePresenceOf('port', 1)) {
        $errors->addError(lang('Port is required'), 'port');
      }//iif
      
      if(!$this->validatePresenceOf('server_type')) {
        $errors->addError(lang('Server type is required'), 'server_type');
      } // if
      
      if(!$this->validatePresenceOf('security')) {
        $errors->addError(lang('Security type is required'), 'security');
      } // if
      
      
      
      if(!$this->validatePresenceOf('email')) {
        $errors->addError(lang('Email is required'), 'email');
      } else {
        $current_user = Users::findByEmail($this->getEmail(), true); // Validate that user does not exist
        if ($current_user instanceof User) {
          $errors->addError(lang('Email is already used by :name user', array('name' => $current_user->getName())), 'email');
        } // if
      } // if

      parent::validate($errors, true);
    } // validate
    
    /**
     * Delete mailbox and its mails
     * 
     */
    function delete() {
      try {
        DB::beginWork('Deleting mailbox @ ' . __CLASS__);
      
        // Delete all incoming mails from this mailbox
        DB::execute('DELETE FROM ' . TABLE_PREFIX . "incoming_mails WHERE incoming_mailbox_id = ?", $this->getId());

        parent::delete();
        DB::commit('Mailbox deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete mailbox @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    }//delete
    
  }