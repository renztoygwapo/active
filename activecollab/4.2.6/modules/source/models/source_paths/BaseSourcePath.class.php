<?php

  /**
   * BaseSourcePath class
   *
   * @package ActiveCollab.modules.source
   * @subpackage models
   */
  abstract class BaseSourcePath extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'source_paths';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'commit_id', 'is_dir', 'path', 'action');
    
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
        return $underscore ? 'source_path' : 'SourcePath';
      } else {
        return $underscore ? 'source_paths' : 'SourcePaths';
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
     * Return value of commit_id field
     *
     * @return integer
     */
    function getCommitId() {
      return $this->getFieldValue('commit_id');
    } // getCommitId
    
    /**
     * Set value of commit_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCommitId($value) {
      return $this->setFieldValue('commit_id', $value);
    } // setCommitId

    /**
     * Return value of is_dir field
     *
     * @return boolean
     */
    function getIsDir() {
      return $this->getFieldValue('is_dir');
    } // getIsDir
    
    /**
     * Set value of is_dir field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDir($value) {
      return $this->setFieldValue('is_dir', $value);
    } // setIsDir

    /**
     * Return value of path field
     *
     * @return string
     */
    function getPath() {
      return $this->getFieldValue('path');
    } // getPath
    
    /**
     * Set value of path field
     *
     * @param string $value
     * @return string
     */
    function setPath($value) {
      return $this->setFieldValue('path', $value);
    } // setPath

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
        case 'commit_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'is_dir':
          return parent::setFieldValue($real_name, (boolean) $value);
        case 'path':
          return parent::setFieldValue($real_name, (string) $value);
        case 'action':
          return parent::setFieldValue($real_name, (string) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }