<?php

  /**
   * BaseSharedObjectProfile class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseSharedObjectProfile extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'shared_object_profiles';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'sharing_context', 'sharing_code', 'expires_on', 'raw_additional_properties', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'is_discoverable');
    
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
        return $underscore ? 'shared_object_profile' : 'SharedObjectProfile';
      } else {
        return $underscore ? 'shared_object_profiles' : 'SharedObjectProfiles';
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
     * Return value of sharing_context field
     *
     * @return string
     */
    function getSharingContext() {
      return $this->getFieldValue('sharing_context');
    } // getSharingContext
    
    /**
     * Set value of sharing_context field
     *
     * @param string $value
     * @return string
     */
    function setSharingContext($value) {
      return $this->setFieldValue('sharing_context', $value);
    } // setSharingContext

    /**
     * Return value of sharing_code field
     *
     * @return string
     */
    function getSharingCode() {
      return $this->getFieldValue('sharing_code');
    } // getSharingCode
    
    /**
     * Set value of sharing_code field
     *
     * @param string $value
     * @return string
     */
    function setSharingCode($value) {
      return $this->setFieldValue('sharing_code', $value);
    } // setSharingCode

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
     * Return value of raw_additional_properties field
     *
     * @return string
     */
    function getRawAdditionalProperties() {
      return $this->getFieldValue('raw_additional_properties');
    } // getRawAdditionalProperties
    
    /**
     * Set value of raw_additional_properties field
     *
     * @param string $value
     * @return string
     */
    function setRawAdditionalProperties($value) {
      return $this->setFieldValue('raw_additional_properties', $value);
    } // setRawAdditionalProperties

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
     * Return value of is_discoverable field
     *
     * @return boolean
     */
    function getIsDiscoverable() {
      return $this->getFieldValue('is_discoverable');
    } // getIsDiscoverable
    
    /**
     * Set value of is_discoverable field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDiscoverable($value) {
      return $this->setFieldValue('is_discoverable', $value);
    } // setIsDiscoverable

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
          case 'sharing_context':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sharing_code':
            return parent::setFieldValue($real_name, (string) $value);
          case 'expires_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_discoverable':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }