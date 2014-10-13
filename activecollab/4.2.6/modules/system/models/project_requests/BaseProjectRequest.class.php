<?php

  /**
   * BaseProjectRequest class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseProjectRequest extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'project_requests';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'public_id', 'name', 'body', 'status', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'created_by_company_id', 'created_by_company_name', 'created_by_company_address', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5', 'is_locked', 'taken_by_id', 'taken_by_name', 'taken_by_email', 'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email', 'last_comment_on');
    
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
        return $underscore ? 'project_request' : 'ProjectRequest';
      } else {
        return $underscore ? 'project_requests' : 'ProjectRequests';
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
     * Return value of public_id field
     *
     * @return string
     */
    function getPublicId() {
      return $this->getFieldValue('public_id');
    } // getPublicId
    
    /**
     * Set value of public_id field
     *
     * @param string $value
     * @return string
     */
    function setPublicId($value) {
      return $this->setFieldValue('public_id', $value);
    } // setPublicId

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
     * Return value of status field
     *
     * @return integer
     */
    function getStatus() {
      return $this->getFieldValue('status');
    } // getStatus
    
    /**
     * Set value of status field
     *
     * @param integer $value
     * @return integer
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    } // setStatus

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
     * Return value of created_by_company_id field
     *
     * @return integer
     */
    function getCreatedByCompanyId() {
      return $this->getFieldValue('created_by_company_id');
    } // getCreatedByCompanyId
    
    /**
     * Set value of created_by_company_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCreatedByCompanyId($value) {
      return $this->setFieldValue('created_by_company_id', $value);
    } // setCreatedByCompanyId

    /**
     * Return value of created_by_company_name field
     *
     * @return string
     */
    function getCreatedByCompanyName() {
      return $this->getFieldValue('created_by_company_name');
    } // getCreatedByCompanyName
    
    /**
     * Set value of created_by_company_name field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByCompanyName($value) {
      return $this->setFieldValue('created_by_company_name', $value);
    } // setCreatedByCompanyName

    /**
     * Return value of created_by_company_address field
     *
     * @return string
     */
    function getCreatedByCompanyAddress() {
      return $this->getFieldValue('created_by_company_address');
    } // getCreatedByCompanyAddress
    
    /**
     * Set value of created_by_company_address field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByCompanyAddress($value) {
      return $this->setFieldValue('created_by_company_address', $value);
    } // setCreatedByCompanyAddress

    /**
     * Return value of custom_field_1 field
     *
     * @return string
     */
    function getCustomField1() {
      return $this->getFieldValue('custom_field_1');
    } // getCustomField1
    
    /**
     * Set value of custom_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setCustomField1($value) {
      return $this->setFieldValue('custom_field_1', $value);
    } // setCustomField1

    /**
     * Return value of custom_field_2 field
     *
     * @return string
     */
    function getCustomField2() {
      return $this->getFieldValue('custom_field_2');
    } // getCustomField2
    
    /**
     * Set value of custom_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setCustomField2($value) {
      return $this->setFieldValue('custom_field_2', $value);
    } // setCustomField2

    /**
     * Return value of custom_field_3 field
     *
     * @return string
     */
    function getCustomField3() {
      return $this->getFieldValue('custom_field_3');
    } // getCustomField3
    
    /**
     * Set value of custom_field_3 field
     *
     * @param string $value
     * @return string
     */
    function setCustomField3($value) {
      return $this->setFieldValue('custom_field_3', $value);
    } // setCustomField3

    /**
     * Return value of custom_field_4 field
     *
     * @return string
     */
    function getCustomField4() {
      return $this->getFieldValue('custom_field_4');
    } // getCustomField4
    
    /**
     * Set value of custom_field_4 field
     *
     * @param string $value
     * @return string
     */
    function setCustomField4($value) {
      return $this->setFieldValue('custom_field_4', $value);
    } // setCustomField4

    /**
     * Return value of custom_field_5 field
     *
     * @return string
     */
    function getCustomField5() {
      return $this->getFieldValue('custom_field_5');
    } // getCustomField5
    
    /**
     * Set value of custom_field_5 field
     *
     * @param string $value
     * @return string
     */
    function setCustomField5($value) {
      return $this->setFieldValue('custom_field_5', $value);
    } // setCustomField5

    /**
     * Return value of is_locked field
     *
     * @return boolean
     */
    function getIsLocked() {
      return $this->getFieldValue('is_locked');
    } // getIsLocked
    
    /**
     * Set value of is_locked field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsLocked($value) {
      return $this->setFieldValue('is_locked', $value);
    } // setIsLocked

    /**
     * Return value of taken_by_id field
     *
     * @return integer
     */
    function getTakenById() {
      return $this->getFieldValue('taken_by_id');
    } // getTakenById
    
    /**
     * Set value of taken_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setTakenById($value) {
      return $this->setFieldValue('taken_by_id', $value);
    } // setTakenById

    /**
     * Return value of taken_by_name field
     *
     * @return string
     */
    function getTakenByName() {
      return $this->getFieldValue('taken_by_name');
    } // getTakenByName
    
    /**
     * Set value of taken_by_name field
     *
     * @param string $value
     * @return string
     */
    function setTakenByName($value) {
      return $this->setFieldValue('taken_by_name', $value);
    } // setTakenByName

    /**
     * Return value of taken_by_email field
     *
     * @return string
     */
    function getTakenByEmail() {
      return $this->getFieldValue('taken_by_email');
    } // getTakenByEmail
    
    /**
     * Set value of taken_by_email field
     *
     * @param string $value
     * @return string
     */
    function setTakenByEmail($value) {
      return $this->setFieldValue('taken_by_email', $value);
    } // setTakenByEmail

    /**
     * Return value of closed_on field
     *
     * @return DateTimeValue
     */
    function getClosedOn() {
      return $this->getFieldValue('closed_on');
    } // getClosedOn
    
    /**
     * Set value of closed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setClosedOn($value) {
      return $this->setFieldValue('closed_on', $value);
    } // setClosedOn

    /**
     * Return value of closed_by_id field
     *
     * @return integer
     */
    function getClosedById() {
      return $this->getFieldValue('closed_by_id');
    } // getClosedById
    
    /**
     * Set value of closed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setClosedById($value) {
      return $this->setFieldValue('closed_by_id', $value);
    } // setClosedById

    /**
     * Return value of closed_by_name field
     *
     * @return string
     */
    function getClosedByName() {
      return $this->getFieldValue('closed_by_name');
    } // getClosedByName
    
    /**
     * Set value of closed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setClosedByName($value) {
      return $this->setFieldValue('closed_by_name', $value);
    } // setClosedByName

    /**
     * Return value of closed_by_email field
     *
     * @return string
     */
    function getClosedByEmail() {
      return $this->getFieldValue('closed_by_email');
    } // getClosedByEmail
    
    /**
     * Set value of closed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setClosedByEmail($value) {
      return $this->setFieldValue('closed_by_email', $value);
    } // setClosedByEmail

    /**
     * Return value of last_comment_on field
     *
     * @return DateTimeValue
     */
    function getLastCommentOn() {
      return $this->getFieldValue('last_comment_on');
    } // getLastCommentOn
    
    /**
     * Set value of last_comment_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastCommentOn($value) {
      return $this->setFieldValue('last_comment_on', $value);
    } // setLastCommentOn

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
          case 'public_id':
            return parent::setFieldValue($real_name, (string) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'status':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_company_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_company_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_company_address':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_4':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_5':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_locked':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'taken_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'taken_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'taken_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'closed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'closed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'closed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'closed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'last_comment_on':
            return parent::setFieldValue($real_name, datetimeval($value));
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }