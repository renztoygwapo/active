<?php

  /**
   * BaseApiClientSubscription class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseApiClientSubscription extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'api_client_subscriptions';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'user_id', 'token', 'client_name', 'client_vendor', 'created_on', 'last_used_on', 'is_enabled', 'is_read_only');
    
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
        return $underscore ? 'api_client_subscription' : 'ApiClientSubscription';
      } else {
        return $underscore ? 'api_client_subscriptions' : 'ApiClientSubscriptions';
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
     * Return value of token field
     *
     * @return string
     */
    function getToken() {
      return $this->getFieldValue('token');
    } // getToken
    
    /**
     * Set value of token field
     *
     * @param string $value
     * @return string
     */
    function setToken($value) {
      return $this->setFieldValue('token', $value);
    } // setToken

    /**
     * Return value of client_name field
     *
     * @return string
     */
    function getClientName() {
      return $this->getFieldValue('client_name');
    } // getClientName
    
    /**
     * Set value of client_name field
     *
     * @param string $value
     * @return string
     */
    function setClientName($value) {
      return $this->setFieldValue('client_name', $value);
    } // setClientName

    /**
     * Return value of client_vendor field
     *
     * @return string
     */
    function getClientVendor() {
      return $this->getFieldValue('client_vendor');
    } // getClientVendor
    
    /**
     * Set value of client_vendor field
     *
     * @param string $value
     * @return string
     */
    function setClientVendor($value) {
      return $this->setFieldValue('client_vendor', $value);
    } // setClientVendor

    /**
     * Return value of created_on field
     *
     * @return DateTimeValue
     */
    function getCreatedOn() {
      return $this->getFieldValue('created_on');
    } // getCreatedOn
    
    /**
     * Set value of created_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCreatedOn($value) {
      return $this->setFieldValue('created_on', $value);
    } // setCreatedOn

    /**
     * Return value of last_used_on field
     *
     * @return DateTimeValue
     */
    function getLastUsedOn() {
      return $this->getFieldValue('last_used_on');
    } // getLastUsedOn
    
    /**
     * Set value of last_used_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastUsedOn($value) {
      return $this->setFieldValue('last_used_on', $value);
    } // setLastUsedOn

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
     * Return value of is_read_only field
     *
     * @return boolean
     */
    function getIsReadOnly() {
      return $this->getFieldValue('is_read_only');
    } // getIsReadOnly
    
    /**
     * Set value of is_read_only field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsReadOnly($value) {
      return $this->setFieldValue('is_read_only', $value);
    } // setIsReadOnly

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
          case 'user_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'token':
            return parent::setFieldValue($real_name, (string) $value);
          case 'client_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'client_vendor':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'last_used_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'is_read_only':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }