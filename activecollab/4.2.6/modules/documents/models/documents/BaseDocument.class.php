<?php

  /**
   * BaseDocument class
   *
   * @package ActiveCollab.modules.documents
   * @subpackage models
   */
  abstract class BaseDocument extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'documents';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'category_id', 'type', 'name', 'body', 'size', 'mime_type', 'location', 'md5', 'state', 'original_state', 'visibility', 'original_visibility', 'is_pinned', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
        return $underscore ? 'document' : 'Document';
      } else {
        return $underscore ? 'documents' : 'Documents';
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
     * Return value of category_id field
     *
     * @return integer
     */
    function getCategoryId() {
      return $this->getFieldValue('category_id');
    } // getCategoryId
    
    /**
     * Set value of category_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCategoryId($value) {
      return $this->setFieldValue('category_id', $value);
    } // setCategoryId

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
     * Return value of visibility field
     *
     * @return integer
     */
    function getVisibility() {
      return $this->getFieldValue('visibility');
    } // getVisibility
    
    /**
     * Set value of visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setVisibility($value) {
      return $this->setFieldValue('visibility', $value);
    } // setVisibility

    /**
     * Return value of original_visibility field
     *
     * @return integer
     */
    function getOriginalVisibility() {
      return $this->getFieldValue('original_visibility');
    } // getOriginalVisibility
    
    /**
     * Set value of original_visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setOriginalVisibility($value) {
      return $this->setFieldValue('original_visibility', $value);
    } // setOriginalVisibility

    /**
     * Return value of is_pinned field
     *
     * @return boolean
     */
    function getIsPinned() {
      return $this->getFieldValue('is_pinned');
    } // getIsPinned
    
    /**
     * Set value of is_pinned field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsPinned($value) {
      return $this->setFieldValue('is_pinned', $value);
    } // setIsPinned

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
          case 'category_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'type':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'size':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'mime_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'location':
            return parent::setFieldValue($real_name, (string) $value);
          case 'md5':
            return parent::setFieldValue($real_name, (string) $value);
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_visibility':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'is_pinned':
            return parent::setFieldValue($real_name, (boolean) $value);
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