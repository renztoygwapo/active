<?php

  /**
   * BaseEstimate class
   *
   * @package ActiveCollab.modules.tracking
   * @subpackage models
   */
  abstract class BaseEstimate extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'estimates';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'job_type_id', 'value', 'comment', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'estimate' : 'Estimate';
      } else {
        return $underscore ? 'estimates' : 'Estimates';
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
     * Return value of job_type_id field
     *
     * @return integer
     */
    function getJobTypeId() {
      return $this->getFieldValue('job_type_id');
    } // getJobTypeId
    
    /**
     * Set value of job_type_id field
     *
     * @param integer $value
     * @return integer
     */
    function setJobTypeId($value) {
      return $this->setFieldValue('job_type_id', $value);
    } // setJobTypeId

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
     * Return value of comment field
     *
     * @return string
     */
    function getComment() {
      return $this->getFieldValue('comment');
    } // getComment
    
    /**
     * Set value of comment field
     *
     * @param string $value
     * @return string
     */
    function setComment($value) {
      return $this->setFieldValue('comment', $value);
    } // setComment

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
        case 'job_type_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'value':
          return parent::setFieldValue($real_name, (float) $value);
        case 'comment':
          return parent::setFieldValue($real_name, (string) $value);
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