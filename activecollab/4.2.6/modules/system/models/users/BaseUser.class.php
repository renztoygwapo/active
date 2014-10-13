<?php

  /**
   * BaseUser class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseUser extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'users';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'state', 'original_state', 'first_name', 'last_name', 'email', 'password', 'password_hashed_with', 'password_expires_on', 'password_reset_key', 'password_reset_on', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'invited_on', 'last_login_on', 'last_visit_on', 'last_activity_on', 'raw_additional_properties', 'company_id', 'auto_assign', 'auto_assign_role_id', 'auto_assign_permissions', 'managed_by_id', 'personality_type', 'private_url', 'private_url_enabled');
    
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
        return $underscore ? 'user' : 'User';
      } else {
        return $underscore ? 'users' : 'Users';
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
     * Return value of state field
     *
     * @return integer
     */
    function getState() {
      return $this->getFieldValue('state');
    } // getState
    
    /**
     * Set value of state field
     *
     * @param integer $value
     * @return integer
     */
    function setState($value) {
      return $this->setFieldValue('state', $value);
    } // setState

    /**
     * Return value of original_state field
     *
     * @return integer
     */
    function getOriginalState() {
      return $this->getFieldValue('original_state');
    } // getOriginalState
    
    /**
     * Set value of original_state field
     *
     * @param integer $value
     * @return integer
     */
    function setOriginalState($value) {
      return $this->setFieldValue('original_state', $value);
    } // setOriginalState

    /**
     * Return value of first_name field
     *
     * @return string
     */
    function getFirstName() {
      return $this->getFieldValue('first_name');
    } // getFirstName
    
    /**
     * Set value of first_name field
     *
     * @param string $value
     * @return string
     */
    function setFirstName($value) {
      return $this->setFieldValue('first_name', $value);
    } // setFirstName

    /**
     * Return value of last_name field
     *
     * @return string
     */
    function getLastName() {
      return $this->getFieldValue('last_name');
    } // getLastName
    
    /**
     * Set value of last_name field
     *
     * @param string $value
     * @return string
     */
    function setLastName($value) {
      return $this->setFieldValue('last_name', $value);
    } // setLastName

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
     * Return value of password_hashed_with field
     *
     * @return string
     */
    function getPasswordHashedWith() {
      return $this->getFieldValue('password_hashed_with');
    } // getPasswordHashedWith
    
    /**
     * Set value of password_hashed_with field
     *
     * @param string $value
     * @return string
     */
    function setPasswordHashedWith($value) {
      return $this->setFieldValue('password_hashed_with', $value);
    } // setPasswordHashedWith

    /**
     * Return value of password_expires_on field
     *
     * @return DateValue
     */
    function getPasswordExpiresOn() {
      return $this->getFieldValue('password_expires_on');
    } // getPasswordExpiresOn
    
    /**
     * Set value of password_expires_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setPasswordExpiresOn($value) {
      return $this->setFieldValue('password_expires_on', $value);
    } // setPasswordExpiresOn

    /**
     * Return value of password_reset_key field
     *
     * @return string
     */
    function getPasswordResetKey() {
      return $this->getFieldValue('password_reset_key');
    } // getPasswordResetKey
    
    /**
     * Set value of password_reset_key field
     *
     * @param string $value
     * @return string
     */
    function setPasswordResetKey($value) {
      return $this->setFieldValue('password_reset_key', $value);
    } // setPasswordResetKey

    /**
     * Return value of password_reset_on field
     *
     * @return DateTimeValue
     */
    function getPasswordResetOn() {
      return $this->getFieldValue('password_reset_on');
    } // getPasswordResetOn
    
    /**
     * Set value of password_reset_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setPasswordResetOn($value) {
      return $this->setFieldValue('password_reset_on', $value);
    } // setPasswordResetOn

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
     * Return value of updated_on field
     *
     * @return DateTimeValue
     */
    function getUpdatedOn() {
      return $this->getFieldValue('updated_on');
    } // getUpdatedOn
    
    /**
     * Set value of updated_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setUpdatedOn($value) {
      return $this->setFieldValue('updated_on', $value);
    } // setUpdatedOn

    /**
     * Return value of updated_by_id field
     *
     * @return integer
     */
    function getUpdatedById() {
      return $this->getFieldValue('updated_by_id');
    } // getUpdatedById
    
    /**
     * Set value of updated_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setUpdatedById($value) {
      return $this->setFieldValue('updated_by_id', $value);
    } // setUpdatedById

    /**
     * Return value of updated_by_name field
     *
     * @return string
     */
    function getUpdatedByName() {
      return $this->getFieldValue('updated_by_name');
    } // getUpdatedByName
    
    /**
     * Set value of updated_by_name field
     *
     * @param string $value
     * @return string
     */
    function setUpdatedByName($value) {
      return $this->setFieldValue('updated_by_name', $value);
    } // setUpdatedByName

    /**
     * Return value of updated_by_email field
     *
     * @return string
     */
    function getUpdatedByEmail() {
      return $this->getFieldValue('updated_by_email');
    } // getUpdatedByEmail
    
    /**
     * Set value of updated_by_email field
     *
     * @param string $value
     * @return string
     */
    function setUpdatedByEmail($value) {
      return $this->setFieldValue('updated_by_email', $value);
    } // setUpdatedByEmail

    /**
     * Return value of invited_on field
     *
     * @return DateTimeValue
     */
    function getInvitedOn() {
      return $this->getFieldValue('invited_on');
    } // getInvitedOn
    
    /**
     * Set value of invited_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setInvitedOn($value) {
      return $this->setFieldValue('invited_on', $value);
    } // setInvitedOn

    /**
     * Return value of last_login_on field
     *
     * @return DateTimeValue
     */
    function getLastLoginOn() {
      return $this->getFieldValue('last_login_on');
    } // getLastLoginOn
    
    /**
     * Set value of last_login_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastLoginOn($value) {
      return $this->setFieldValue('last_login_on', $value);
    } // setLastLoginOn

    /**
     * Return value of last_visit_on field
     *
     * @return DateTimeValue
     */
    function getLastVisitOn() {
      return $this->getFieldValue('last_visit_on');
    } // getLastVisitOn
    
    /**
     * Set value of last_visit_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastVisitOn($value) {
      return $this->setFieldValue('last_visit_on', $value);
    } // setLastVisitOn

    /**
     * Return value of last_activity_on field
     *
     * @return DateTimeValue
     */
    function getLastActivityOn() {
      return $this->getFieldValue('last_activity_on');
    } // getLastActivityOn
    
    /**
     * Set value of last_activity_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastActivityOn($value) {
      return $this->setFieldValue('last_activity_on', $value);
    } // setLastActivityOn

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
     * Return value of company_id field
     *
     * @return integer
     */
    function getCompanyId() {
      return $this->getFieldValue('company_id');
    } // getCompanyId
    
    /**
     * Set value of company_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompanyId($value) {
      return $this->setFieldValue('company_id', $value);
    } // setCompanyId

    /**
     * Return value of auto_assign field
     *
     * @return boolean
     */
    function getAutoAssign() {
      return $this->getFieldValue('auto_assign');
    } // getAutoAssign
    
    /**
     * Set value of auto_assign field
     *
     * @param boolean $value
     * @return boolean
     */
    function setAutoAssign($value) {
      return $this->setFieldValue('auto_assign', $value);
    } // setAutoAssign

    /**
     * Return value of auto_assign_role_id field
     *
     * @return integer
     */
    function getAutoAssignRoleId() {
      return $this->getFieldValue('auto_assign_role_id');
    } // getAutoAssignRoleId
    
    /**
     * Set value of auto_assign_role_id field
     *
     * @param integer $value
     * @return integer
     */
    function setAutoAssignRoleId($value) {
      return $this->setFieldValue('auto_assign_role_id', $value);
    } // setAutoAssignRoleId

    /**
     * Return value of auto_assign_permissions field
     *
     * @return string
     */
    function getAutoAssignPermissions() {
      return $this->getFieldValue('auto_assign_permissions');
    } // getAutoAssignPermissions
    
    /**
     * Set value of auto_assign_permissions field
     *
     * @param string $value
     * @return string
     */
    function setAutoAssignPermissions($value) {
      return $this->setFieldValue('auto_assign_permissions', $value);
    } // setAutoAssignPermissions



     /**
     * Return value of managed_by_id field
     *
     * @return boolean
     */
    function getManagedById() {
      return $this->getFieldValue('managed_by_id');
    } // getManagedById
    
    /**
     * Set value of managed_by_id field
     *
     * @param boolean $value
     * @return boolean
     */
    function setManagedById($value) {
      return $this->setFieldValue('managed_by_id', $value);
    } // setManagedById



    /**
     * Return value of personality_type field
     *
     * @return boolean
     */
    function getPersonalityType() {
      return $this->getFieldValue('personality_type');
    } // getPersonalityType
    
    /**
     * Set value of personality_type field
     *
     * @param boolean $value
     * @return boolean
     */
    function setPersonalityType($value) {
      return $this->setFieldValue('personality_type', $value);
    } // setPersonalityType


    /**
     * Return value of private_url field
     *
     * @return boolean
     */
    function getPrivateUrl() {
      return $this->getFieldValue('private_url');
    } // getPrivateUrl
    
    /**
     * Set value of private_url field
     *
     * @param boolean $value
     * @return boolean
     */
    function setPrivateUrl($value) {
      return $this->setFieldValue('private_url', $value);
    } // setPrivateUrl


    /**
     * Return value of private_url_enabled field
     *
     * @return boolean
     */
    function getPrivateUrlEnabled() {
      return $this->getFieldValue('private_url_enabled');
    } // getPrivateUrlEnabled
    
    /**
     * Set value of private_url_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setPrivateUrlEnabled($value) {
      return $this->setFieldValue('private_url_enabled', $value);
    } // setPrivateUrlEnabled


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
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'first_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'last_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'password':
            return parent::setFieldValue($real_name, (string) $value);
          case 'password_hashed_with':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'password_expires_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'password_reset_key':
            return parent::setFieldValue($real_name, (string) $value);
          case 'password_reset_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'updated_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'updated_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'updated_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'updated_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'invited_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'last_login_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'last_visit_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'last_activity_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
          case 'company_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'auto_assign':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'auto_assign_role_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'auto_assign_permissions':
            return parent::setFieldValue($real_name, (string) $value);
          case 'managed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'personality_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'private_url':
            return parent::setFieldValue($real_name, (string) $value);
          case 'private_url_enabled':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }