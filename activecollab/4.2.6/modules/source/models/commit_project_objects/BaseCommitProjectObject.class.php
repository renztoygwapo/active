<?php

  /**
   * BaseCommitProjectObject class
   *
   * @package ActiveCollab.modules.source
   * @subpackage models
   */
  abstract class BaseCommitProjectObject extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'commit_project_objects';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_id', 'parent_type', 'project_id', 'revision', 'branch_name', 'repository_id');
    
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
        return $underscore ? 'commit_project_object' : 'CommitProjectObject';
      } else {
        return $underscore ? 'commit_project_objects' : 'CommitProjectObjects';
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
     * Return value of revision field
     *
     * @return integer
     */
    function getRevision() {
      return $this->getFieldValue('revision');
    } // getRevision
    
    /**
     * Set value of revision field
     *
     * @param integer $value
     * @return integer
     */
    function setRevision($value) {
      return $this->setFieldValue('revision', $value);
    } // setRevision

    /**
     * Return value of branch_name field
     *
     * @return string
     */
    function getBranchName() {
      return $this->getFieldValue('branch_name');
    } // getBranchName
    
    /**
     * Set value of branch_name field
     *
     * @param string $value
     * @return string
     */
    function setBranchName($value) {
      return $this->setFieldValue('branch_name', $value);
    } // setBranchName

    /**
     * Return value of repository_id field
     *
     * @return integer
     */
    function getRepositoryId() {
      return $this->getFieldValue('repository_id');
    } // getRepositoryId
    
    /**
     * Set value of repository_id field
     *
     * @param integer $value
     * @return integer
     */
    function setRepositoryId($value) {
      return $this->setFieldValue('repository_id', $value);
    } // setRepositoryId

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
        case 'parent_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'parent_type':
          return parent::setFieldValue($real_name, (string) $value);
        case 'project_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'revision':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'branch_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'repository_id':
          return parent::setFieldValue($real_name, (integer) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }