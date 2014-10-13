<?php

  /**
   * BaseSourceUser class
   *
   * @package ActiveCollab.modules.source
   * @subpackage models
   */
  abstract class BaseSourceUser extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'source_users';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'repository_id', 'repository_user', 'user_id');
    
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
        return $underscore ? 'source_user' : 'SourceUser';
      } else {
        return $underscore ? 'source_users' : 'SourceUsers';
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
     * Return value of repository_user field
     *
     * @return string
     */
    function getRepositoryUser() {
      return $this->getFieldValue('repository_user');
    } // getRepositoryUser
    
    /**
     * Set value of repository_user field
     *
     * @param string $value
     * @return string
     */
    function setRepositoryUser($value) {
      return $this->setFieldValue('repository_user', $value);
    } // setRepositoryUser

    /**
     * Return value of user_id field
     *
     * @return integer
     */
    function getUserId() {
      return $this->getFieldValue('user_id');
    } // getUserId
    
    /**
     * Set value of user_id field
     *
     * @param integer $value
     * @return integer
     */
    function setUserId($value) {
      return $this->setFieldValue('user_id', $value);
    } // setUserId

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
        case 'repository_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'repository_user':
          return parent::setFieldValue($real_name, (string) $value);
        case 'user_id':
          return parent::setFieldValue($real_name, (integer) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }