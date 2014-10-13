<?php

  /**
   * BaseNotebookPage class
   *
   * @package ActiveCollab.modules.notebooks
   * @subpackage models
   */
  abstract class BaseNotebookPage extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'notebook_pages';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'name', 'body', 'state', 'original_state', 'is_locked', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'last_version_on', 'last_version_by_id', 'last_version_by_name', 'last_version_by_email', 'position', 'version');
    
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
        return $underscore ? 'notebook_page' : 'NotebookPage';
      } else {
        return $underscore ? 'notebook_pages' : 'NotebookPages';
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
     * Return value of last_version_on field
     *
     * @return DateTimeValue
     */
    function getLastVersionOn() {
      return $this->getFieldValue('last_version_on');
    } // getLastVersionOn
    
    /**
     * Set value of last_version_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastVersionOn($value) {
      return $this->setFieldValue('last_version_on', $value);
    } // setLastVersionOn

    /**
     * Return value of last_version_by_id field
     *
     * @return integer
     */
    function getLastVersionById() {
      return $this->getFieldValue('last_version_by_id');
    } // getLastVersionById
    
    /**
     * Set value of last_version_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLastVersionById($value) {
      return $this->setFieldValue('last_version_by_id', $value);
    } // setLastVersionById

    /**
     * Return value of last_version_by_name field
     *
     * @return string
     */
    function getLastVersionByName() {
      return $this->getFieldValue('last_version_by_name');
    } // getLastVersionByName
    
    /**
     * Set value of last_version_by_name field
     *
     * @param string $value
     * @return string
     */
    function setLastVersionByName($value) {
      return $this->setFieldValue('last_version_by_name', $value);
    } // setLastVersionByName

    /**
     * Return value of last_version_by_email field
     *
     * @return string
     */
    function getLastVersionByEmail() {
      return $this->getFieldValue('last_version_by_email');
    } // getLastVersionByEmail
    
    /**
     * Set value of last_version_by_email field
     *
     * @param string $value
     * @return string
     */
    function setLastVersionByEmail($value) {
      return $this->setFieldValue('last_version_by_email', $value);
    } // setLastVersionByEmail

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
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'parent_type':
          return parent::setFieldValue($real_name, (string) $value);
        case 'parent_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'body':
          return parent::setFieldValue($real_name, (string) $value);
        case 'state':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'original_state':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'is_locked':
          return parent::setFieldValue($real_name, (boolean) $value);
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
        case 'last_version_on':
          return parent::setFieldValue($real_name, datetimeval($value));
        case 'last_version_by_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'last_version_by_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'last_version_by_email':
          return parent::setFieldValue($real_name, (string) $value);
        case 'position':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'version':
          return parent::setFieldValue($real_name, (integer) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }