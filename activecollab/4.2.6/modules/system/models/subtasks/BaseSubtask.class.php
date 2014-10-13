<?php

  /**
   * BaseSubtask class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseSubtask extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'subtasks';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'parent_type', 'parent_id', 'label_id', 'assignee_id', 'delegated_by_id', 'priority', 'body', 'due_on', 'state', 'original_state', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 'position');
    
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
        return $underscore ? 'subtask' : 'Subtask';
      } else {
        return $underscore ? 'subtasks' : 'Subtasks';
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
     * Return value of label_id field
     *
     * @return integer
     */
    function getLabelId() {
      return $this->getFieldValue('label_id');
    } // getLabelId
    
    /**
     * Set value of label_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLabelId($value) {
      return $this->setFieldValue('label_id', $value);
    } // setLabelId

    /**
     * Return value of assignee_id field
     *
     * @return integer
     */
    function getAssigneeId() {
      return $this->getFieldValue('assignee_id');
    } // getAssigneeId
    
    /**
     * Set value of assignee_id field
     *
     * @param integer $value
     * @return integer
     */
    function setAssigneeId($value) {
      return $this->setFieldValue('assignee_id', $value);
    } // setAssigneeId

    /**
     * Return value of delegated_by_id field
     *
     * @return integer
     */
    function getDelegatedById() {
      return $this->getFieldValue('delegated_by_id');
    } // getDelegatedById
    
    /**
     * Set value of delegated_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setDelegatedById($value) {
      return $this->setFieldValue('delegated_by_id', $value);
    } // setDelegatedById

    /**
     * Return value of priority field
     *
     * @return integer
     */
    function getPriority() {
      return $this->getFieldValue('priority');
    } // getPriority
    
    /**
     * Set value of priority field
     *
     * @param integer $value
     * @return integer
     */
    function setPriority($value) {
      return $this->setFieldValue('priority', $value);
    } // setPriority

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
     * Return value of due_on field
     *
     * @return DateValue
     */
    function getDueOn() {
      return $this->getFieldValue('due_on');
    } // getDueOn
    
    /**
     * Set value of due_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDueOn($value) {
      return $this->setFieldValue('due_on', $value);
    } // setDueOn

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
     * Return value of completed_on field
     *
     * @return DateTimeValue
     */
    function getCompletedOn() {
      return $this->getFieldValue('completed_on');
    } // getCompletedOn
    
    /**
     * Set value of completed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCompletedOn($value) {
      return $this->setFieldValue('completed_on', $value);
    } // setCompletedOn

    /**
     * Return value of completed_by_id field
     *
     * @return integer
     */
    function getCompletedById() {
      return $this->getFieldValue('completed_by_id');
    } // getCompletedById
    
    /**
     * Set value of completed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompletedById($value) {
      return $this->setFieldValue('completed_by_id', $value);
    } // setCompletedById

    /**
     * Return value of completed_by_name field
     *
     * @return string
     */
    function getCompletedByName() {
      return $this->getFieldValue('completed_by_name');
    } // getCompletedByName
    
    /**
     * Set value of completed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByName($value) {
      return $this->setFieldValue('completed_by_name', $value);
    } // setCompletedByName

    /**
     * Return value of completed_by_email field
     *
     * @return string
     */
    function getCompletedByEmail() {
      return $this->getFieldValue('completed_by_email');
    } // getCompletedByEmail
    
    /**
     * Set value of completed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByEmail($value) {
      return $this->setFieldValue('completed_by_email', $value);
    } // setCompletedByEmail

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
          case 'type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'label_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'assignee_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'delegated_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'priority':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'due_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
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
          case 'completed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'completed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'completed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'completed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }