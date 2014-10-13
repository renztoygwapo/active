<?php

  /**
   * BaseFileVersion class
   *
   * @package ActiveCollab.modules.files
   * @subpackage models
   */
  abstract class BaseFileVersion extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'file_versions';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'file_id', 'version_num', 'name', 'mime_type', 'size', 'location', 'md5', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'file_version' : 'FileVersion';
      } else {
        return $underscore ? 'file_versions' : 'FileVersions';
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
     * Return value of file_id field
     *
     * @return integer
     */
    function getFileId() {
      return $this->getFieldValue('file_id');
    } // getFileId
    
    /**
     * Set value of file_id field
     *
     * @param integer $value
     * @return integer
     */
    function setFileId($value) {
      return $this->setFieldValue('file_id', $value);
    } // setFileId

    /**
     * Return value of version_num field
     *
     * @return integer
     */
    function getVersionNum() {
      return $this->getFieldValue('version_num');
    } // getVersionNum
    
    /**
     * Set value of version_num field
     *
     * @param integer $value
     * @return integer
     */
    function setVersionNum($value) {
      return $this->setFieldValue('version_num', $value);
    } // setVersionNum

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
     * Return value of mime_type field
     *
     * @return string
     */
    function getMimeType() {
      return $this->getFieldValue('mime_type');
    } // getMimeType
    
    /**
     * Set value of mime_type field
     *
     * @param string $value
     * @return string
     */
    function setMimeType($value) {
      return $this->setFieldValue('mime_type', $value);
    } // setMimeType

    /**
     * Return value of size field
     *
     * @return integer
     */
    function getSize() {
      return $this->getFieldValue('size');
    } // getSize
    
    /**
     * Set value of size field
     *
     * @param integer $value
     * @return integer
     */
    function setSize($value) {
      return $this->setFieldValue('size', $value);
    } // setSize

    /**
     * Return value of location field
     *
     * @return string
     */
    function getLocation() {
      return $this->getFieldValue('location');
    } // getLocation
    
    /**
     * Set value of location field
     *
     * @param string $value
     * @return string
     */
    function setLocation($value) {
      return $this->setFieldValue('location', $value);
    } // setLocation

    /**
     * Return value of md5 field
     *
     * @return string
     */
    function getMd5() {
      return $this->getFieldValue('md5');
    } // getMd5
    
    /**
     * Set value of md5 field
     *
     * @param string $value
     * @return string
     */
    function setMd5($value) {
      return $this->setFieldValue('md5', $value);
    } // setMd5

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
          case 'file_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'version_num':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mime_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'size':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'location':
            return parent::setFieldValue($real_name, (string) $value);
          case 'md5':
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