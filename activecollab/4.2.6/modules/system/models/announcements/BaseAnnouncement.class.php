<?php

  /**
   * BaseAnnouncement class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseAnnouncement extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'announcements';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'subject', 'body', 'body_type', 'icon', 'target_type', 'expiration_type', 'expires_on', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'is_enabled', 'position');
    
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
        return $underscore ? 'announcement' : 'Announcement';
      } else {
        return $underscore ? 'announcements' : 'Announcements';
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
     * Return value of subject field
     *
     * @return string
     */
    function getSubject() {
      return $this->getFieldValue('subject');
    } // getSubject
    
    /**
     * Set value of subject field
     *
     * @param string $value
     * @return string
     */
    function setSubject($value) {
      return $this->setFieldValue('subject', $value);
    } // setSubject

    /**
     * Return value of body field
     *
     * @return string
     */
    function getBody() {
      return $this->getFieldValue('body');
    } // getBody
    
    /**
     * Set value of body field
     *
     * @param string $value
     * @return string
     */
    function setBody($value) {
      return $this->setFieldValue('body', $value);
    } // setBody

    /**
     * Return value of body_type field
     *
     * @return integer
     */
    function getBodyType() {
      return $this->getFieldValue('body_type');
    } // getBodyType
    
    /**
     * Set value of body_type field
     *
     * @param integer $value
     * @return integer
     */
    function setBodyType($value) {
      return $this->setFieldValue('body_type', $value);
    } // setBodyType

    /**
     * Return value of icon field
     *
     * @return string
     */
    function getIcon() {
      return $this->getFieldValue('icon');
    } // getIcon
    
    /**
     * Set value of icon field
     *
     * @param string $value
     * @return string
     */
    function setIcon($value) {
      return $this->setFieldValue('icon', $value);
    } // setIcon

    /**
     * Return value of target_type field
     *
     * @return string
     */
    function getTargetType() {
      return $this->getFieldValue('target_type');
    } // getTargetType
    
    /**
     * Set value of target_type field
     *
     * @param string $value
     * @return string
     */
    function setTargetType($value) {
      return $this->setFieldValue('target_type', $value);
    } // setTargetType

    /**
     * Return value of expiration_type field
     *
     * @return string
     */
    function getExpirationType() {
      return $this->getFieldValue('expiration_type');
    } // getExpirationType
    
    /**
     * Set value of expiration_type field
     *
     * @param string $value
     * @return string
     */
    function setExpirationType($value) {
      return $this->setFieldValue('expiration_type', $value);
    } // setExpirationType

    /**
     * Return value of expires_on field
     *
     * @return DateValue
     */
    function getExpiresOn() {
      return $this->getFieldValue('expires_on');
    } // getExpiresOn
    
    /**
     * Set value of expires_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setExpiresOn($value) {
      return $this->setFieldValue('expires_on', $value);
    } // setExpiresOn

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
     * Return value of created_by_id field
     *
     * @return integer
     */
    function getCreatedById() {
      return $this->getFieldValue('created_by_id');
    } // getCreatedById
    
    /**
     * Set value of created_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCreatedById($value) {
      return $this->setFieldValue('created_by_id', $value);
    } // setCreatedById

    /**
     * Return value of created_by_name field
     *
     * @return string
     */
    function getCreatedByName() {
      return $this->getFieldValue('created_by_name');
    } // getCreatedByName
    
    /**
     * Set value of created_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByName($value) {
      return $this->setFieldValue('created_by_name', $value);
    } // setCreatedByName

    /**
     * Return value of created_by_email field
     *
     * @return string
     */
    function getCreatedByEmail() {
      return $this->getFieldValue('created_by_email');
    } // getCreatedByEmail
    
    /**
     * Set value of created_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByEmail($value) {
      return $this->setFieldValue('created_by_email', $value);
    } // setCreatedByEmail

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
     * Return value of position field
     *
     * @return integer
     */
    function getPosition() {
      return $this->getFieldValue('position');
    } // getPosition
    
    /**
     * Set value of position field
     *
     * @param integer $value
     * @return integer
     */
    function setPosition($value) {
      return $this->setFieldValue('position', $value);
    } // setPosition

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
          case 'subject':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body_type':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'icon':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'target_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'expiration_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'expires_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }