<?php

  /**
   * BaseSubscription class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseSubscription extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'subscriptions';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'user_id', 'user_name', 'user_email', 'subscribed_on', 'code');
    
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
        return $underscore ? 'subscription' : 'Subscription';
      } else {
        return $underscore ? 'subscriptions' : 'Subscriptions';
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
     * Return value of parent_type field
     *
     * @return string
     */
    function getParentType() {
      return $this->getFieldValue('parent_type');
    } // getParentType
    
    /**
     * Set value of parent_type field
     *
     * @param string $value
     * @return string
     */
    function setParentType($value) {
      return $this->setFieldValue('parent_type', $value);
    } // setParentType

    /**
     * Return value of parent_id field
     *
     * @return integer
     */
    function getParentId() {
      return $this->getFieldValue('parent_id');
    } // getParentId
    
    /**
     * Set value of parent_id field
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setFieldValue('parent_id', $value);
    } // setParentId

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
     * Return value of subscribed_on field
     *
     * @return DateTimeValue
     */
    function getSubscribedOn() {
      return $this->getFieldValue('subscribed_on');
    } // getSubscribedOn
    
    /**
     * Set value of subscribed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setSubscribedOn($value) {
      return $this->setFieldValue('subscribed_on', $value);
    } // setSubscribedOn

    /**
     * Return value of code field
     *
     * @return string
     */
    function getCode() {
      return $this->getFieldValue('code');
    } // getCode
    
    /**
     * Set value of code field
     *
     * @param string $value
     * @return string
     */
    function setCode($value) {
      return $this->setFieldValue('code', $value);
    } // setCode

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
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'user_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'user_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'user_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'subscribed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'code':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }