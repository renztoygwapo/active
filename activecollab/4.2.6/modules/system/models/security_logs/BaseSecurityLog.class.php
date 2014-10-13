<?php

  /**
   * BaseSecurityLog class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseSecurityLog extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'security_logs';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'user_id', 'user_name', 'user_email', 'login_as_id', 'login_as_name', 'login_as_email', 'logout_by_id', 'logout_by_name', 'logout_by_email', 'event', 'event_on', 'user_ip', 'user_agent', 'is_api');
    
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
        return $underscore ? 'security_log' : 'SecurityLog';
      } else {
        return $underscore ? 'security_logs' : 'SecurityLogs';
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
     * Return value of user_id field
     *
     * @return integer
     */
    function getUserId() {
      return $this->getFieldValue('user_id');
    } // getUserId
    
    /**
     * Set value of user_id field
     *
     * @param integer $value
     * @return integer
     */
    function setUserId($value) {
      return $this->setFieldValue('user_id', $value);
    } // setUserId

    /**
     * Return value of user_name field
     *
     * @return string
     */
    function getUserName() {
      return $this->getFieldValue('user_name');
    } // getUserName
    
    /**
     * Set value of user_name field
     *
     * @param string $value
     * @return string
     */
    function setUserName($value) {
      return $this->setFieldValue('user_name', $value);
    } // setUserName

    /**
     * Return value of user_email field
     *
     * @return string
     */
    function getUserEmail() {
      return $this->getFieldValue('user_email');
    } // getUserEmail
    
    /**
     * Set value of user_email field
     *
     * @param string $value
     * @return string
     */
    function setUserEmail($value) {
      return $this->setFieldValue('user_email', $value);
    } // setUserEmail

    /**
     * Return value of login_as_id field
     *
     * @return integer
     */
    function getLoginAsId() {
      return $this->getFieldValue('login_as_id');
    } // getLoginAsId
    
    /**
     * Set value of login_as_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLoginAsId($value) {
      return $this->setFieldValue('login_as_id', $value);
    } // setLoginAsId

    /**
     * Return value of login_as_name field
     *
     * @return string
     */
    function getLoginAsName() {
      return $this->getFieldValue('login_as_name');
    } // getLoginAsName
    
    /**
     * Set value of login_as_name field
     *
     * @param string $value
     * @return string
     */
    function setLoginAsName($value) {
      return $this->setFieldValue('login_as_name', $value);
    } // setLoginAsName

    /**
     * Return value of login_as_email field
     *
     * @return string
     */
    function getLoginAsEmail() {
      return $this->getFieldValue('login_as_email');
    } // getLoginAsEmail
    
    /**
     * Set value of login_as_email field
     *
     * @param string $value
     * @return string
     */
    function setLoginAsEmail($value) {
      return $this->setFieldValue('login_as_email', $value);
    } // setLoginAsEmail

    /**
     * Return value of logout_by_id field
     *
     * @return integer
     */
    function getLogoutById() {
      return $this->getFieldValue('logout_by_id');
    } // getLogoutById
    
    /**
     * Set value of logout_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLogoutById($value) {
      return $this->setFieldValue('logout_by_id', $value);
    } // setLogoutById

    /**
     * Return value of logout_by_name field
     *
     * @return string
     */
    function getLogoutByName() {
      return $this->getFieldValue('logout_by_name');
    } // getLogoutByName
    
    /**
     * Set value of logout_by_name field
     *
     * @param string $value
     * @return string
     */
    function setLogoutByName($value) {
      return $this->setFieldValue('logout_by_name', $value);
    } // setLogoutByName

    /**
     * Return value of logout_by_email field
     *
     * @return string
     */
    function getLogoutByEmail() {
      return $this->getFieldValue('logout_by_email');
    } // getLogoutByEmail
    
    /**
     * Set value of logout_by_email field
     *
     * @param string $value
     * @return string
     */
    function setLogoutByEmail($value) {
      return $this->setFieldValue('logout_by_email', $value);
    } // setLogoutByEmail

    /**
     * Return value of event field
     *
     * @return string
     */
    function getEvent() {
      return $this->getFieldValue('event');
    } // getEvent
    
    /**
     * Set value of event field
     *
     * @param string $value
     * @return string
     */
    function setEvent($value) {
      return $this->setFieldValue('event', $value);
    } // setEvent

    /**
     * Return value of event_on field
     *
     * @return DateTimeValue
     */
    function getEventOn() {
      return $this->getFieldValue('event_on');
    } // getEventOn
    
    /**
     * Set value of event_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setEventOn($value) {
      return $this->setFieldValue('event_on', $value);
    } // setEventOn

    /**
     * Return value of user_ip field
     *
     * @return string
     */
    function getUserIp() {
      return $this->getFieldValue('user_ip');
    } // getUserIp
    
    /**
     * Set value of user_ip field
     *
     * @param string $value
     * @return string
     */
    function setUserIp($value) {
      return $this->setFieldValue('user_ip', $value);
    } // setUserIp

    /**
     * Return value of user_agent field
     *
     * @return string
     */
    function getUserAgent() {
      return $this->getFieldValue('user_agent');
    } // getUserAgent
    
    /**
     * Set value of user_agent field
     *
     * @param string $value
     * @return string
     */
    function setUserAgent($value) {
      return $this->setFieldValue('user_agent', $value);
    } // setUserAgent

    /**
     * Return value of is_api field
     *
     * @return boolean
     */
    function getIsApi() {
      return $this->getFieldValue('is_api');
    } // getIsApi
    
    /**
     * Set value of is_api field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsApi($value) {
      return $this->setFieldValue('is_api', $value);
    } // setIsApi

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
          case 'user_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'user_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'user_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'login_as_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'login_as_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'login_as_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'logout_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'logout_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'logout_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'event':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'event_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'user_ip':
            return parent::setFieldValue($real_name, (string) $value);
          case 'user_agent':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_api':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }