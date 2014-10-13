<?php

  /**
   * BaseExpense class
   *
   * @package ActiveCollab.modules.tracking
   * @subpackage models
   */
  abstract class BaseExpense extends TrackingObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'expenses';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'category_id', 'state', 'original_state', 'record_date', 'value', 'user_id', 'user_name', 'user_email', 'summary', 'billable_status', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'expense' : 'Expense';
      } else {
        return $underscore ? 'expenses' : 'Expenses';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    

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
     * Return value of category_id field
     *
     * @return integer
     */
    function getCategoryId() {
      return $this->getFieldValue('category_id');
    } // getCategoryId
    
    /**
     * Set value of category_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCategoryId($value) {
      return $this->setFieldValue('category_id', $value);
    } // setCategoryId

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
     * Return value of record_date field
     *
     * @return DateValue
     */
    function getRecordDate() {
      return $this->getFieldValue('record_date');
    } // getRecordDate
    
    /**
     * Set value of record_date field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setRecordDate($value) {
      return $this->setFieldValue('record_date', $value);
    } // setRecordDate

    /**
     * Return value of value field
     *
     * @return float
     */
    function getValue() {
      return $this->getFieldValue('value');
    } // getValue
    
    /**
     * Set value of value field
     *
     * @param float $value
     * @return float
     */
    function setValue($value) {
      return $this->setFieldValue('value', $value);
    } // setValue

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
     * Return value of summary field
     *
     * @return string
     */
    function getSummary() {
      return $this->getFieldValue('summary');
    } // getSummary
    
    /**
     * Set value of summary field
     *
     * @param string $value
     * @return string
     */
    function setSummary($value) {
      return $this->setFieldValue('summary', $value);
    } // setSummary

    /**
     * Return value of billable_status field
     *
     * @return integer
     */
    function getBillableStatus() {
      return $this->getFieldValue('billable_status');
    } // getBillableStatus
    
    /**
     * Set value of billable_status field
     *
     * @param integer $value
     * @return integer
     */
    function setBillableStatus($value) {
      return $this->setFieldValue('billable_status', $value);
    } // setBillableStatus

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
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'parent_type':
          return parent::setFieldValue($real_name, (string) $value);
        case 'parent_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'category_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'state':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'original_state':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'record_date':
          return parent::setFieldValue($real_name, dateval($value));
        case 'value':
          return parent::setFieldValue($real_name, (float) $value);
        case 'user_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'user_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'user_email':
          return parent::setFieldValue($real_name, (string) $value);
        case 'summary':
          return parent::setFieldValue($real_name, (string) $value);
        case 'billable_status':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'created_on':
          return parent::setFieldValue($real_name, datetimeval($value));
        case 'created_by_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'created_by_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'created_by_email':
          return parent::setFieldValue($real_name, (string) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }