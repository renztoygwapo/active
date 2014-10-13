<?php

  /**
   * BaseProjectObject class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseProjectObject extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'project_objects';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'source', 'module', 'project_id', 'milestone_id', 'category_id', 'label_id', 'assignee_id', 'delegated_by_id', 'name', 'body', 'state', 'original_state', 'visibility', 'original_visibility', 'priority', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'due_on', 'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 'is_locked', 'varchar_field_1', 'varchar_field_2', 'varchar_field_3', 'integer_field_1', 'integer_field_2', 'integer_field_3', 'float_field_1', 'float_field_2', 'float_field_3', 'text_field_1', 'text_field_2', 'text_field_3', 'date_field_1', 'date_field_2', 'date_field_3', 'datetime_field_1', 'datetime_field_2', 'datetime_field_3', 'boolean_field_1', 'boolean_field_2', 'boolean_field_3', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'position', 'version');
    
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
        return $underscore ? 'project_object' : 'ProjectObject';
      } else {
        return $underscore ? 'project_objects' : 'ProjectObjects';
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
     * Return value of source field
     *
     * @return string
     */
    function getSource() {
      return $this->getFieldValue('source');
    } // getSource
    
    /**
     * Set value of source field
     *
     * @param string $value
     * @return string
     */
    function setSource($value) {
      return $this->setFieldValue('source', $value);
    } // setSource

    /**
     * Return value of module field
     *
     * @return string
     */
    function getModule() {
      return $this->getFieldValue('module');
    } // getModule
    
    /**
     * Set value of module field
     *
     * @param string $value
     * @return string
     */
    function setModule($value) {
      return $this->setFieldValue('module', $value);
    } // setModule

    /**
     * Return value of project_id field
     *
     * @return integer
     */
    function getProjectId() {
      return $this->getFieldValue('project_id');
    } // getProjectId
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
      return $this->setFieldValue('project_id', $value);
    } // setProjectId

    /**
     * Return value of milestone_id field
     *
     * @return integer
     */
    function getMilestoneId() {
      return $this->getFieldValue('milestone_id');
    } // getMilestoneId
    
    /**
     * Set value of milestone_id field
     *
     * @param integer $value
     * @return integer
     */
    function setMilestoneId($value) {
      return $this->setFieldValue('milestone_id', $value);
    } // setMilestoneId

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
     * Return value of visibility field
     *
     * @return integer
     */
    function getVisibility() {
      return $this->getFieldValue('visibility');
    } // getVisibility
    
    /**
     * Set value of visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setVisibility($value) {
      return $this->setFieldValue('visibility', $value);
    } // setVisibility

    /**
     * Return value of original_visibility field
     *
     * @return integer
     */
    function getOriginalVisibility() {
      return $this->getFieldValue('original_visibility');
    } // getOriginalVisibility
    
    /**
     * Set value of original_visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setOriginalVisibility($value) {
      return $this->setFieldValue('original_visibility', $value);
    } // setOriginalVisibility

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
     * Return value of varchar_field_1 field
     *
     * @return string
     */
    function getVarcharField1() {
      return $this->getFieldValue('varchar_field_1');
    } // getVarcharField1
    
    /**
     * Set value of varchar_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField1($value) {
      return $this->setFieldValue('varchar_field_1', $value);
    } // setVarcharField1

    /**
     * Return value of varchar_field_2 field
     *
     * @return string
     */
    function getVarcharField2() {
      return $this->getFieldValue('varchar_field_2');
    } // getVarcharField2
    
    /**
     * Set value of varchar_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField2($value) {
      return $this->setFieldValue('varchar_field_2', $value);
    } // setVarcharField2

    /**
     * Return value of varchar_field_3 field
     *
     * @return string
     */
    function getVarcharField3() {
      return $this->getFieldValue('varchar_field_3');
    } // getVarcharField3
    
    /**
     * Set value of varchar_field_3 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField3($value) {
      return $this->setFieldValue('varchar_field_3', $value);
    } // setVarcharField3

    /**
     * Return value of integer_field_1 field
     *
     * @return integer
     */
    function getIntegerField1() {
      return $this->getFieldValue('integer_field_1');
    } // getIntegerField1
    
    /**
     * Set value of integer_field_1 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField1($value) {
      return $this->setFieldValue('integer_field_1', $value);
    } // setIntegerField1

    /**
     * Return value of integer_field_2 field
     *
     * @return integer
     */
    function getIntegerField2() {
      return $this->getFieldValue('integer_field_2');
    } // getIntegerField2
    
    /**
     * Set value of integer_field_2 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField2($value) {
      return $this->setFieldValue('integer_field_2', $value);
    } // setIntegerField2

    /**
     * Return value of integer_field_3 field
     *
     * @return integer
     */
    function getIntegerField3() {
      return $this->getFieldValue('integer_field_3');
    } // getIntegerField3
    
    /**
     * Set value of integer_field_3 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField3($value) {
      return $this->setFieldValue('integer_field_3', $value);
    } // setIntegerField3

    /**
     * Return value of float_field_1 field
     *
     * @return float
     */
    function getFloatField1() {
      return $this->getFieldValue('float_field_1');
    } // getFloatField1
    
    /**
     * Set value of float_field_1 field
     *
     * @param float $value
     * @return float
     */
    function setFloatField1($value) {
      return $this->setFieldValue('float_field_1', $value);
    } // setFloatField1

    /**
     * Return value of float_field_2 field
     *
     * @return float
     */
    function getFloatField2() {
      return $this->getFieldValue('float_field_2');
    } // getFloatField2
    
    /**
     * Set value of float_field_2 field
     *
     * @param float $value
     * @return float
     */
    function setFloatField2($value) {
      return $this->setFieldValue('float_field_2', $value);
    } // setFloatField2

    /**
     * Return value of float_field_3 field
     *
     * @return float
     */
    function getFloatField3() {
      return $this->getFieldValue('float_field_3');
    } // getFloatField3
    
    /**
     * Set value of float_field_3 field
     *
     * @param float $value
     * @return float
     */
    function setFloatField3($value) {
      return $this->setFieldValue('float_field_3', $value);
    } // setFloatField3

    /**
     * Return value of text_field_1 field
     *
     * @return string
     */
    function getTextField1() {
      return $this->getFieldValue('text_field_1');
    } // getTextField1
    
    /**
     * Set value of text_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setTextField1($value) {
      return $this->setFieldValue('text_field_1', $value);
    } // setTextField1

    /**
     * Return value of text_field_2 field
     *
     * @return string
     */
    function getTextField2() {
      return $this->getFieldValue('text_field_2');
    } // getTextField2
    
    /**
     * Set value of text_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setTextField2($value) {
      return $this->setFieldValue('text_field_2', $value);
    } // setTextField2

    /**
     * Return value of text_field_3 field
     *
     * @return string
     */
    function getTextField3() {
      return $this->getFieldValue('text_field_3');
    } // getTextField3
    
    /**
     * Set value of text_field_3 field
     *
     * @param string $value
     * @return string
     */
    function setTextField3($value) {
      return $this->setFieldValue('text_field_3', $value);
    } // setTextField3

    /**
     * Return value of date_field_1 field
     *
     * @return DateValue
     */
    function getDateField1() {
      return $this->getFieldValue('date_field_1');
    } // getDateField1
    
    /**
     * Set value of date_field_1 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField1($value) {
      return $this->setFieldValue('date_field_1', $value);
    } // setDateField1

    /**
     * Return value of date_field_2 field
     *
     * @return DateValue
     */
    function getDateField2() {
      return $this->getFieldValue('date_field_2');
    } // getDateField2
    
    /**
     * Set value of date_field_2 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField2($value) {
      return $this->setFieldValue('date_field_2', $value);
    } // setDateField2

    /**
     * Return value of date_field_3 field
     *
     * @return DateValue
     */
    function getDateField3() {
      return $this->getFieldValue('date_field_3');
    } // getDateField3
    
    /**
     * Set value of date_field_3 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField3($value) {
      return $this->setFieldValue('date_field_3', $value);
    } // setDateField3

    /**
     * Return value of datetime_field_1 field
     *
     * @return DateTimeValue
     */
    function getDatetimeField1() {
      return $this->getFieldValue('datetime_field_1');
    } // getDatetimeField1
    
    /**
     * Set value of datetime_field_1 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField1($value) {
      return $this->setFieldValue('datetime_field_1', $value);
    } // setDatetimeField1

    /**
     * Return value of datetime_field_2 field
     *
     * @return DateTimeValue
     */
    function getDatetimeField2() {
      return $this->getFieldValue('datetime_field_2');
    } // getDatetimeField2
    
    /**
     * Set value of datetime_field_2 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField2($value) {
      return $this->setFieldValue('datetime_field_2', $value);
    } // setDatetimeField2

    /**
     * Return value of datetime_field_3 field
     *
     * @return DateTimeValue
     */
    function getDatetimeField3() {
      return $this->getFieldValue('datetime_field_3');
    } // getDatetimeField3
    
    /**
     * Set value of datetime_field_3 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField3($value) {
      return $this->setFieldValue('datetime_field_3', $value);
    } // setDatetimeField3

    /**
     * Return value of boolean_field_1 field
     *
     * @return boolean
     */
    function getBooleanField1() {
      return $this->getFieldValue('boolean_field_1');
    } // getBooleanField1
    
    /**
     * Set value of boolean_field_1 field
     *
     * @param boolean $value
     * @return boolean
     */
    function setBooleanField1($value) {
      return $this->setFieldValue('boolean_field_1', $value);
    } // setBooleanField1

    /**
     * Return value of boolean_field_2 field
     *
     * @return boolean
     */
    function getBooleanField2() {
      return $this->getFieldValue('boolean_field_2');
    } // getBooleanField2
    
    /**
     * Set value of boolean_field_2 field
     *
     * @param boolean $value
     * @return boolean
     */
    function setBooleanField2($value) {
      return $this->setFieldValue('boolean_field_2', $value);
    } // setBooleanField2

    /**
     * Return value of boolean_field_3 field
     *
     * @return boolean
     */
    function getBooleanField3() {
      return $this->getFieldValue('boolean_field_3');
    } // getBooleanField3
    
    /**
     * Set value of boolean_field_3 field
     *
     * @param boolean $value
     * @return boolean
     */
    function setBooleanField3($value) {
      return $this->setFieldValue('boolean_field_3', $value);
    } // setBooleanField3

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
     * Return value of version field
     *
     * @return integer
     */
    function getVersion() {
      return $this->getFieldValue('version');
    } // getVersion
    
    /**
     * Set value of version field
     *
     * @param integer $value
     * @return integer
     */
    function setVersion($value) {
      return $this->setFieldValue('version', $value);
    } // setVersion

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
          case 'source':
            return parent::setFieldValue($real_name, (string) $value);
          case 'module':
            return parent::setFieldValue($real_name, (string) $value);
          case 'project_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'milestone_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'category_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'label_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'assignee_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'delegated_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'priority':
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
          case 'due_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'completed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'completed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'completed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'completed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_locked':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'varchar_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'varchar_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'integer_field_1':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'integer_field_2':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'integer_field_3':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'float_field_1':
            return parent::setFieldValue($real_name, (float) $value);
          case 'float_field_2':
            return parent::setFieldValue($real_name, (float) $value);
          case 'float_field_3':
            return parent::setFieldValue($real_name, (float) $value);
          case 'text_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'text_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'text_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'date_field_1':
            return parent::setFieldValue($real_name, dateval($value));
          case 'date_field_2':
            return parent::setFieldValue($real_name, dateval($value));
          case 'date_field_3':
            return parent::setFieldValue($real_name, dateval($value));
          case 'datetime_field_1':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'datetime_field_2':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'datetime_field_3':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'boolean_field_1':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'boolean_field_2':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'boolean_field_3':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'custom_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'version':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }