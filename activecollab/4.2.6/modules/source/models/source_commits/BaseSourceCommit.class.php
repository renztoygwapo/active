<?php

  /**
   * BaseSourceCommit class
   *
   * @package ActiveCollab.modules.source
   * @subpackage models
   */
  abstract class BaseSourceCommit extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'source_commits';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'type', 'revision_number', 'repository_id', 'message_title', 'message_body', 'authored_on', 'authored_by_name', 'authored_by_email', 'commited_on', 'commited_by_name', 'commited_by_email', 'branch_name', 'diff');
    
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
        return $underscore ? 'source_commit' : 'SourceCommit';
      } else {
        return $underscore ? 'source_commits' : 'SourceCommits';
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
     * Return value of revision_number field
     *
     * @return integer
     */
    function getRevisionNumber() {
      return $this->getFieldValue('revision_number');
    } // getRevisionNumber
    
    /**
     * Set value of revision_number field
     *
     * @param integer $value
     * @return integer
     */
    function setRevisionNumber($value) {
      return $this->setFieldValue('revision_number', $value);
    } // setRevisionNumber

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
     * Return value of message_title field
     *
     * @return string
     */
    function getMessageTitle() {
      return $this->getFieldValue('message_title');
    } // getMessageTitle
    
    /**
     * Set value of message_title field
     *
     * @param string $value
     * @return string
     */
    function setMessageTitle($value) {
      return $this->setFieldValue('message_title', $value);
    } // setMessageTitle

    /**
     * Return value of message_body field
     *
     * @return string
     */
    function getMessageBody() {
      return $this->getFieldValue('message_body');
    } // getMessageBody
    
    /**
     * Set value of message_body field
     *
     * @param string $value
     * @return string
     */
    function setMessageBody($value) {
      return $this->setFieldValue('message_body', $value);
    } // setMessageBody

    /**
     * Return value of authored_on field
     *
     * @return DateTimeValue
     */
    function getAuthoredOn() {
      return $this->getFieldValue('authored_on');
    } // getAuthoredOn
    
    /**
     * Set value of authored_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setAuthoredOn($value) {
      return $this->setFieldValue('authored_on', $value);
    } // setAuthoredOn

    /**
     * Return value of authored_by_name field
     *
     * @return string
     */
    function getAuthoredByName() {
      return $this->getFieldValue('authored_by_name');
    } // getAuthoredByName
    
    /**
     * Set value of authored_by_name field
     *
     * @param string $value
     * @return string
     */
    function setAuthoredByName($value) {
      return $this->setFieldValue('authored_by_name', $value);
    } // setAuthoredByName

    /**
     * Return value of authored_by_email field
     *
     * @return string
     */
    function getAuthoredByEmail() {
      return $this->getFieldValue('authored_by_email');
    } // getAuthoredByEmail
    
    /**
     * Set value of authored_by_email field
     *
     * @param string $value
     * @return string
     */
    function setAuthoredByEmail($value) {
      return $this->setFieldValue('authored_by_email', $value);
    } // setAuthoredByEmail

    /**
     * Return value of commited_on field
     *
     * @return DateTimeValue
     */
    function getCommitedOn() {
      return $this->getFieldValue('commited_on');
    } // getCommitedOn
    
    /**
     * Set value of commited_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCommitedOn($value) {
      return $this->setFieldValue('commited_on', $value);
    } // setCommitedOn

    /**
     * Return value of commited_by_name field
     *
     * @return string
     */
    function getCommitedByName() {
      return $this->getFieldValue('commited_by_name');
    } // getCommitedByName
    
    /**
     * Set value of commited_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCommitedByName($value) {
      return $this->setFieldValue('commited_by_name', $value);
    } // setCommitedByName

    /**
     * Return value of commited_by_email field
     *
     * @return string
     */
    function getCommitedByEmail() {
      return $this->getFieldValue('commited_by_email');
    } // getCommitedByEmail
    
    /**
     * Set value of commited_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCommitedByEmail($value) {
      return $this->setFieldValue('commited_by_email', $value);
    } // setCommitedByEmail

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
     * Return value of diff field
     *
     * @return string
     */
    function getDiff() {
      return $this->getFieldValue('diff');
    } // getDiff
    
    /**
     * Set value of diff field
     *
     * @param string $value
     * @return string
     */
    function setDiff($value) {
      return $this->setFieldValue('diff', $value);
    } // setDiff

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
        case 'revision_number':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'repository_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'message_title':
          return parent::setFieldValue($real_name, (string) $value);
        case 'message_body':
          return parent::setFieldValue($real_name, (string) $value);
        case 'authored_on':
          return parent::setFieldValue($real_name, datetimeval($value));
        case 'authored_by_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'authored_by_email':
          return parent::setFieldValue($real_name, (string) $value);
        case 'commited_on':
          return parent::setFieldValue($real_name, datetimeval($value));
        case 'commited_by_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'commited_by_email':
          return parent::setFieldValue($real_name, (string) $value);
        case 'branch_name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'diff':
          return parent::setFieldValue($real_name, (string) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }