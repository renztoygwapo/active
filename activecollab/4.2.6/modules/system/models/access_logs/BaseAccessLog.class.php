<?php

  /**
   * BaseAccessLog class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseAccessLog extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'access_logs';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'accessed_by_id', 'accessed_by_name', 'accessed_by_email', 'accessed_on', 'ip_address', 'is_download');
    
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
        return $underscore ? 'access_log' : 'AccessLog';
      } else {
        return $underscore ? 'access_logs' : 'AccessLogs';
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
     * Return value of accessed_by_id field
     *
     * @return integer
     */
    function getAccessedById() {
      return $this->getFieldValue('accessed_by_id');
    } // getAccessedById
    
    /**
     * Set value of accessed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setAccessedById($value) {
      return $this->setFieldValue('accessed_by_id', $value);
    } // setAccessedById

    /**
     * Return value of accessed_by_name field
     *
     * @return string
     */
    function getAccessedByName() {
      return $this->getFieldValue('accessed_by_name');
    } // getAccessedByName
    
    /**
     * Set value of accessed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setAccessedByName($value) {
      return $this->setFieldValue('accessed_by_name', $value);
    } // setAccessedByName

    /**
     * Return value of accessed_by_email field
     *
     * @return string
     */
    function getAccessedByEmail() {
      return $this->getFieldValue('accessed_by_email');
    } // getAccessedByEmail
    
    /**
     * Set value of accessed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setAccessedByEmail($value) {
      return $this->setFieldValue('accessed_by_email', $value);
    } // setAccessedByEmail

    /**
     * Return value of accessed_on field
     *
     * @return DateTimeValue
     */
    function getAccessedOn() {
      return $this->getFieldValue('accessed_on');
    } // getAccessedOn
    
    /**
     * Set value of accessed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setAccessedOn($value) {
      return $this->setFieldValue('accessed_on', $value);
    } // setAccessedOn

    /**
     * Return value of ip_address field
     *
     * @return string
     */
    function getIpAddress() {
      return $this->getFieldValue('ip_address');
    } // getIpAddress
    
    /**
     * Set value of ip_address field
     *
     * @param string $value
     * @return string
     */
    function setIpAddress($value) {
      return $this->setFieldValue('ip_address', $value);
    } // setIpAddress

    /**
     * Return value of is_download field
     *
     * @return boolean
     */
    function getIsDownload() {
      return $this->getFieldValue('is_download');
    } // getIsDownload
    
    /**
     * Set value of is_download field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDownload($value) {
      return $this->setFieldValue('is_download', $value);
    } // setIsDownload

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
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'accessed_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'accessed_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'accessed_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'accessed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'ip_address':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_download':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }