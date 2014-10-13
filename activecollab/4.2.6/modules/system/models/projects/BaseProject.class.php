<?php

  /**
   * BaseProject class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseProject extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'projects';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'slug', 'template_id', 'based_on_type', 'based_on_id', 'company_id', 'category_id', 'label_id', 'currency_id', 'budget', 'state', 'original_state', 'name', 'leader_id', 'leader_name', 'leader_email', 'overview', 'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'mail_to_project_code');
    
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
        return $underscore ? 'project' : 'Project';
      } else {
        return $underscore ? 'projects' : 'Projects';
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
     * Return value of slug field
     *
     * @return string
     */
    function getSlug() {
      return $this->getFieldValue('slug');
    } // getSlug
    
    /**
     * Set value of slug field
     *
     * @param string $value
     * @return string
     */
    function setSlug($value) {
      return $this->setFieldValue('slug', $value);
    } // setSlug

    /**
     * Return value of template_id field
     *
     * @return integer
     */
    function getTemplateId() {
      return $this->getFieldValue('template_id');
    } // getTemplateId
    
    /**
     * Set value of template_id field
     *
     * @param integer $value
     * @return integer
     */
    function setTemplateId($value) {
      return $this->setFieldValue('template_id', $value);
    } // setTemplateId

    /**
     * Return value of based_on_type field
     *
     * @return string
     */
    function getBasedOnType() {
      return $this->getFieldValue('based_on_type');
    } // getBasedOnType
    
    /**
     * Set value of based_on_type field
     *
     * @param string $value
     * @return string
     */
    function setBasedOnType($value) {
      return $this->setFieldValue('based_on_type', $value);
    } // setBasedOnType

    /**
     * Return value of based_on_id field
     *
     * @return integer
     */
    function getBasedOnId() {
      return $this->getFieldValue('based_on_id');
    } // getBasedOnId
    
    /**
     * Set value of based_on_id field
     *
     * @param integer $value
     * @return integer
     */
    function setBasedOnId($value) {
      return $this->setFieldValue('based_on_id', $value);
    } // setBasedOnId

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
     * Return value of currency_id field
     *
     * @return integer
     */
    function getCurrencyId() {
      return $this->getFieldValue('currency_id');
    } // getCurrencyId
    
    /**
     * Set value of currency_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCurrencyId($value) {
      return $this->setFieldValue('currency_id', $value);
    } // setCurrencyId

    /**
     * Return value of budget field
     *
     * @return float
     */
    function getBudget() {
      return $this->getFieldValue('budget');
    } // getBudget
    
    /**
     * Set value of budget field
     *
     * @param float $value
     * @return float
     */
    function setBudget($value) {
      return $this->setFieldValue('budget', $value);
    } // setBudget

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
     * Return value of leader_id field
     *
     * @return integer
     */
    function getLeaderId() {
      return $this->getFieldValue('leader_id');
    } // getLeaderId
    
    /**
     * Set value of leader_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLeaderId($value) {
      return $this->setFieldValue('leader_id', $value);
    } // setLeaderId

    /**
     * Return value of leader_name field
     *
     * @return string
     */
    function getLeaderName() {
      return $this->getFieldValue('leader_name');
    } // getLeaderName
    
    /**
     * Set value of leader_name field
     *
     * @param string $value
     * @return string
     */
    function setLeaderName($value) {
      return $this->setFieldValue('leader_name', $value);
    } // setLeaderName

    /**
     * Return value of leader_email field
     *
     * @return string
     */
    function getLeaderEmail() {
      return $this->getFieldValue('leader_email');
    } // getLeaderEmail
    
    /**
     * Set value of leader_email field
     *
     * @param string $value
     * @return string
     */
    function setLeaderEmail($value) {
      return $this->setFieldValue('leader_email', $value);
    } // setLeaderEmail

    /**
     * Return value of overview field
     *
     * @return string
     */
    function getOverview() {
      return $this->getFieldValue('overview');
    } // getOverview
    
    /**
     * Set value of overview field
     *
     * @param string $value
     * @return string
     */
    function setOverview($value) {
      return $this->setFieldValue('overview', $value);
    } // setOverview

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
     * Return value of mail_to_project_code field
     *
     * @return string
     */
    function getMailToProjectCode() {
      return $this->getFieldValue('mail_to_project_code');
    } // getMailToProjectCode
    
    /**
     * Set value of mail_to_project_code field
     *
     * @param string $value
     * @return string
     */
    function setMailToProjectCode($value) {
      return $this->setFieldValue('mail_to_project_code', $value);
    } // setMailToProjectCode

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
          case 'slug':
            return parent::setFieldValue($real_name, (string) $value);
          case 'template_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'based_on_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'based_on_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'company_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'category_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'label_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'currency_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'budget':
            return parent::setFieldValue($real_name, (float) $value);
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'leader_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'leader_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'leader_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'overview':
            return parent::setFieldValue($real_name, (string) $value);
          case 'completed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'completed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'completed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'completed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
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
          case 'custom_field_1':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_2':
            return parent::setFieldValue($real_name, (string) $value);
          case 'custom_field_3':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mail_to_project_code':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }