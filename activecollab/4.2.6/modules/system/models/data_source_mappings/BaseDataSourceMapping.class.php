<?php

  /**
   * BaseDataSourceMapping class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseDataSourceMapping extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'data_source_mappings';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'project_id', 'source_type', 'source_id', 'parent_id', 'parent_type', 'external_id', 'external_type', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'data_source_mapping' : 'DataSourceMapping';
      } else {
        return $underscore ? 'data_source_mappings' : 'DataSourceMappings';
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
     * Return value of source_type field
     *
     * @return string
     */
    function getSourceType() {
      return $this->getFieldValue('source_type');
    } // getSourceType
    
    /**
     * Set value of source_type field
     *
     * @param string $value
     * @return string
     */
    function setSourceType($value) {
      return $this->setFieldValue('source_type', $value);
    } // setSourceType

    /**
     * Return value of source_id field
     *
     * @return integer
     */
    function getSourceId() {
      return $this->getFieldValue('source_id');
    } // getSourceId
    
    /**
     * Set value of source_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSourceId($value) {
      return $this->setFieldValue('source_id', $value);
    } // setSourceId

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
     * Return value of external_id field
     *
     * @return integer
     */
    function getExternalId() {
      return $this->getFieldValue('external_id');
    } // getExternalId
    
    /**
     * Set value of external_id field
     *
     * @param integer $value
     * @return integer
     */
    function setExternalId($value) {
      return $this->setFieldValue('external_id', $value);
    } // setExternalId

    /**
     * Return value of external_type field
     *
     * @return string
     */
    function getExternalType() {
      return $this->getFieldValue('external_type');
    } // getExternalType
    
    /**
     * Set value of external_type field
     *
     * @param string $value
     * @return string
     */
    function setExternalType($value) {
      return $this->setFieldValue('external_type', $value);
    } // setExternalType

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
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
          case 'id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'project_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'source_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'source_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'external_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'external_type':
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
      } // if
    } // setFieldValue
  
  }