<?php

  /**
   * BaseNotebookPageVersion class
   *
   * @package ActiveCollab.modules.notebooks
   * @subpackage models
   */
  abstract class BaseNotebookPageVersion extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'notebook_page_versions';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'notebook_page_id', 'version', 'name', 'body', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'notebook_page_version' : 'NotebookPageVersion';
      } else {
        return $underscore ? 'notebook_page_versions' : 'NotebookPageVersions';
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
     * Return value of notebook_page_id field
     *
     * @return integer
     */
    function getNotebookPageId() {
      return $this->getFieldValue('notebook_page_id');
    } // getNotebookPageId
    
    /**
     * Set value of notebook_page_id field
     *
     * @param integer $value
     * @return integer
     */
    function setNotebookPageId($value) {
      return $this->setFieldValue('notebook_page_id', $value);
    } // setNotebookPageId

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
        case 'notebook_page_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'version':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'body':
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