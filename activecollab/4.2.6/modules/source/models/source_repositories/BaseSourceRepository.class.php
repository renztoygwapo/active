<?php

  /**
   * BaseSourceRepository class
   *
   * @package ActiveCollab.modules.source
   * @subpackage models
   */
  abstract class BaseSourceRepository extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'source_repositories';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'type', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'repository_path_url', 'username', 'password', 'update_type', 'graph', 'raw_additional_properties');
    
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
        return $underscore ? 'source_repository' : 'SourceRepository';
      } else {
        return $underscore ? 'source_repositories' : 'SourceRepositories';
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
     * Return value of repository_path_url field
     *
     * @return string
     */
    function getRepositoryPathUrl() {
      return $this->getFieldValue('repository_path_url');
    } // getRepositoryPathUrl
    
    /**
     * Set value of repository_path_url field
     *
     * @param string $value
     * @return string
     */
    function setRepositoryPathUrl($value) {
      return $this->setFieldValue('repository_path_url', $value);
    } // setRepositoryPathUrl

    /**
     * Return value of username field
     *
     * @return string
     */
    function getUsername() {
      return $this->getFieldValue('username');
    } // getUsername
    
    /**
     * Set value of username field
     *
     * @param string $value
     * @return string
     */
    function setUsername($value) {
      return $this->setFieldValue('username', $value);
    } // setUsername

    /**
     * Return value of password field
     *
     * @return string
     */
    function getPassword() {
      return $this->getFieldValue('password');
    } // getPassword
    
    /**
     * Set value of password field
     *
     * @param string $value
     * @return string
     */
    function setPassword($value) {
      return $this->setFieldValue('password', $value);
    } // setPassword

    /**
     * Return value of update_type field
     *
     * @return integer
     */
    function getUpdateType() {
      return $this->getFieldValue('update_type');
    } // getUpdateType
    
    /**
     * Set value of update_type field
     *
     * @param integer $value
     * @return integer
     */
    function setUpdateType($value) {
      return $this->setFieldValue('update_type', $value);
    } // setUpdateType

    /**
     * Return value of graph field
     *
     * @return string
     */
    function getGraph() {
      return $this->getFieldValue('graph');
    } // getGraph
    
    /**
     * Set value of graph field
     *
     * @param string $value
     * @return string
     */
    function setGraph($value) {
      return $this->setFieldValue('graph', $value);
    } // setGraph

    /**
     * Return value of raw_additional_properties field
     *
     * @return string
     */
    function getRawAdditionalProperties() {
      return $this->getFieldValue('raw_additional_properties');
    } // getRawAdditionalProperties
    
    /**
     * Set value of raw_additional_properties field
     *
     * @param string $value
     * @return string
     */
    function setRawAdditionalProperties($value) {
      return $this->setFieldValue('raw_additional_properties', $value);
    } // setRawAdditionalProperties

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
        case 'name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'type':
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
        case 'repository_path_url':
          return parent::setFieldValue($real_name, (string) $value);
        case 'username':
          return parent::setFieldValue($real_name, (string) $value);
        case 'password':
          return parent::setFieldValue($real_name, (string) $value);
        case 'update_type':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'graph':
          return parent::setFieldValue($real_name, (string) $value);
        case 'raw_additional_properties':
          return parent::setFieldValue($real_name, (string) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }