<?php

  /**
   * BaseIncomingMailbox class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseIncomingMailbox extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'incoming_mailboxes';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'name', 'email', 'mailbox', 'username', 'password', 'host', 'server_type', 'port', 'security', 'last_status', 'is_enabled', 'failure_attempts');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    protected $primary_key = array('id');

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    function getModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? 'incoming_mailbox' : 'IncomingMailbox';
      } else {
        return $underscore ? 'incoming_mailboxes' : 'IncomingMailboxes';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    // ---------------------------------------------------
    //  Fields
    // ---------------------------------------------------

    /**
     * Return value of id field
     *
     * @return integer
     */
    function getId() {
      return $this->getFieldValue('id');
    } // getId
    
    /**
     * Set value of id field
     *
     * @param integer $value
     * @return integer
     */
    function setId($value) {
      return $this->setFieldValue('id', $value);
    } // setId

    /**
     * Return value of type field
     *
     * @return string
     */
    function getType() {
      return $this->getFieldValue('type');
    } // getType
    
    /**
     * Set value of type field
     *
     * @param string $value
     * @return string
     */
    function setType($value) {
      return $this->setFieldValue('type', $value);
    } // setType

    /**
     * Return value of name field
     *
     * @return string
     */
    function getName() {
      return $this->getFieldValue('name');
    } // getName
    
    /**
     * Set value of name field
     *
     * @param string $value
     * @return string
     */
    function setName($value) {
      return $this->setFieldValue('name', $value);
    } // setName

    /**
     * Return value of email field
     *
     * @return string
     */
    function getEmail() {
      return $this->getFieldValue('email');
    } // getEmail
    
    /**
     * Set value of email field
     *
     * @param string $value
     * @return string
     */
    function setEmail($value) {
      return $this->setFieldValue('email', $value);
    } // setEmail

    /**
     * Return value of mailbox field
     *
     * @return string
     */
    function getMailbox() {
      return $this->getFieldValue('mailbox');
    } // getMailbox
    
    /**
     * Set value of mailbox field
     *
     * @param string $value
     * @return string
     */
    function setMailbox($value) {
      return $this->setFieldValue('mailbox', $value);
    } // setMailbox

    /**
     * Return value of username field
     *
     * @return string
     */
    function getUsername() {
      return $this->getFieldValue('username');
    } // getUsername
    
    /**
     * Set value of username field
     *
     * @param string $value
     * @return string
     */
    function setUsername($value) {
      return $this->setFieldValue('username', $value);
    } // setUsername

    /**
     * Return value of password field
     *
     * @return string
     */
    function getPassword() {
      return $this->getFieldValue('password');
    } // getPassword
    
    /**
     * Set value of password field
     *
     * @param string $value
     * @return string
     */
    function setPassword($value) {
      return $this->setFieldValue('password', $value);
    } // setPassword

    /**
     * Return value of host field
     *
     * @return string
     */
    function getHost() {
      return $this->getFieldValue('host');
    } // getHost
    
    /**
     * Set value of host field
     *
     * @param string $value
     * @return string
     */
    function setHost($value) {
      return $this->setFieldValue('host', $value);
    } // setHost

    /**
     * Return value of server_type field
     *
     * @return string
     */
    function getServerType() {
      return $this->getFieldValue('server_type');
    } // getServerType
    
    /**
     * Set value of server_type field
     *
     * @param string $value
     * @return string
     */
    function setServerType($value) {
      return $this->setFieldValue('server_type', $value);
    } // setServerType

    /**
     * Return value of port field
     *
     * @return integer
     */
    function getPort() {
      return $this->getFieldValue('port');
    } // getPort
    
    /**
     * Set value of port field
     *
     * @param integer $value
     * @return integer
     */
    function setPort($value) {
      return $this->setFieldValue('port', $value);
    } // setPort

    /**
     * Return value of security field
     *
     * @return string
     */
    function getSecurity() {
      return $this->getFieldValue('security');
    } // getSecurity
    
    /**
     * Set value of security field
     *
     * @param string $value
     * @return string
     */
    function setSecurity($value) {
      return $this->setFieldValue('security', $value);
    } // setSecurity

    /**
     * Return value of last_status field
     *
     * @return integer
     */
    function getLastStatus() {
      return $this->getFieldValue('last_status');
    } // getLastStatus
    
    /**
     * Set value of last_status field
     *
     * @param integer $value
     * @return integer
     */
    function setLastStatus($value) {
      return $this->setFieldValue('last_status', $value);
    } // setLastStatus

    /**
     * Return value of is_enabled field
     *
     * @return boolean
     */
    function getIsEnabled() {
      return $this->getFieldValue('is_enabled');
    } // getIsEnabled
    
    /**
     * Set value of is_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsEnabled($value) {
      return $this->setFieldValue('is_enabled', $value);
    } // setIsEnabled

    /**
     * Return value of failure_attempts field
     *
     * @return integer
     */
    function getFailureAttempts() {
      return $this->getFieldValue('failure_attempts');
    } // getFailureAttempts
    
    /**
     * Set value of failure_attempts field
     *
     * @param integer $value
     * @return integer
     */
    function setFailureAttempts($value) {
      return $this->setFieldValue('failure_attempts', $value);
    } // setFailureAttempts

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
          case 'id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mailbox':
            return parent::setFieldValue($real_name, (string) $value);
          case 'username':
            return parent::setFieldValue($real_name, (string) $value);
          case 'password':
            return parent::setFieldValue($real_name, (string) $value);
          case 'host':
            return parent::setFieldValue($real_name, (string) $value);
          case 'server_type':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'port':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'security':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'last_status':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'failure_attempts':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }