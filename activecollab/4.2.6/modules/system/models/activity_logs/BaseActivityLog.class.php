<?php

  /**
   * BaseActivityLog class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseActivityLog extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'activity_logs';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'comment');
    
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
        return $underscore ? 'activity_log' : 'ActivityLog';
      } else {
        return $underscore ? 'activity_logs' : 'ActivityLogs';
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
     * Return value of subject_type field
     *
     * @return string
     */
    function getSubjectType() {
      return $this->getFieldValue('subject_type');
    } // getSubjectType
    
    /**
     * Set value of subject_type field
     *
     * @param string $value
     * @return string
     */
    function setSubjectType($value) {
      return $this->setFieldValue('subject_type', $value);
    } // setSubjectType

    /**
     * Return value of subject_id field
     *
     * @return integer
     */
    function getSubjectId() {
      return $this->getFieldValue('subject_id');
    } // getSubjectId
    
    /**
     * Set value of subject_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSubjectId($value) {
      return $this->setFieldValue('subject_id', $value);
    } // setSubjectId

    /**
     * Return value of subject_context field
     *
     * @return string
     */
    function getSubjectContext() {
      return $this->getFieldValue('subject_context');
    } // getSubjectContext
    
    /**
     * Set value of subject_context field
     *
     * @param string $value
     * @return string
     */
    function setSubjectContext($value) {
      return $this->setFieldValue('subject_context', $value);
    } // setSubjectContext

    /**
     * Return value of action field
     *
     * @return string
     */
    function getAction() {
      return $this->getFieldValue('action');
    } // getAction
    
    /**
     * Set value of action field
     *
     * @param string $value
     * @return string
     */
    function setAction($value) {
      return $this->setFieldValue('action', $value);
    } // setAction

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
     * Return value of target_id field
     *
     * @return integer
     */
    function getTargetId() {
      return $this->getFieldValue('target_id');
    } // getTargetId
    
    /**
     * Set value of target_id field
     *
     * @param integer $value
     * @return integer
     */
    function setTargetId($value) {
      return $this->setFieldValue('target_id', $value);
    } // setTargetId

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
          case 'subject_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'subject_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'subject_context':
            return parent::setFieldValue($real_name, (string) $value);
          case 'action':
            return parent::setFieldValue($real_name, (string) $value);
          case 'target_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'target_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'comment':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }