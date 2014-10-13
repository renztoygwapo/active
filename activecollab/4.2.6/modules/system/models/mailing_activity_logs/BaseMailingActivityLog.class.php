<?php

  /**
   * BaseMailingActivityLog class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseMailingActivityLog extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'mailing_activity_logs';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'direction', 'from_id', 'from_name', 'from_email', 'to_id', 'to_name', 'to_email', 'created_on', 'raw_additional_properties');
    
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
        return $underscore ? 'mailing_activity_log' : 'MailingActivityLog';
      } else {
        return $underscore ? 'mailing_activity_logs' : 'MailingActivityLogs';
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
     * Return value of direction field
     *
     * @return string
     */
    function getDirection() {
      return $this->getFieldValue('direction');
    } // getDirection
    
    /**
     * Set value of direction field
     *
     * @param string $value
     * @return string
     */
    function setDirection($value) {
      return $this->setFieldValue('direction', $value);
    } // setDirection

    /**
     * Return value of from_id field
     *
     * @return integer
     */
    function getFromId() {
      return $this->getFieldValue('from_id');
    } // getFromId
    
    /**
     * Set value of from_id field
     *
     * @param integer $value
     * @return integer
     */
    function setFromId($value) {
      return $this->setFieldValue('from_id', $value);
    } // setFromId

    /**
     * Return value of from_name field
     *
     * @return string
     */
    function getFromName() {
      return $this->getFieldValue('from_name');
    } // getFromName
    
    /**
     * Set value of from_name field
     *
     * @param string $value
     * @return string
     */
    function setFromName($value) {
      return $this->setFieldValue('from_name', $value);
    } // setFromName

    /**
     * Return value of from_email field
     *
     * @return string
     */
    function getFromEmail() {
      return $this->getFieldValue('from_email');
    } // getFromEmail
    
    /**
     * Set value of from_email field
     *
     * @param string $value
     * @return string
     */
    function setFromEmail($value) {
      return $this->setFieldValue('from_email', $value);
    } // setFromEmail

    /**
     * Return value of to_id field
     *
     * @return integer
     */
    function getToId() {
      return $this->getFieldValue('to_id');
    } // getToId
    
    /**
     * Set value of to_id field
     *
     * @param integer $value
     * @return integer
     */
    function setToId($value) {
      return $this->setFieldValue('to_id', $value);
    } // setToId

    /**
     * Return value of to_name field
     *
     * @return string
     */
    function getToName() {
      return $this->getFieldValue('to_name');
    } // getToName
    
    /**
     * Set value of to_name field
     *
     * @param string $value
     * @return string
     */
    function setToName($value) {
      return $this->setFieldValue('to_name', $value);
    } // setToName

    /**
     * Return value of to_email field
     *
     * @return string
     */
    function getToEmail() {
      return $this->getFieldValue('to_email');
    } // getToEmail
    
    /**
     * Set value of to_email field
     *
     * @param string $value
     * @return string
     */
    function setToEmail($value) {
      return $this->setFieldValue('to_email', $value);
    } // setToEmail

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
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
          case 'id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'direction':
            return parent::setFieldValue($real_name, (empty($value) ? NULL : (string) $value));
          case 'from_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'from_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'from_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'to_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'to_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'to_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }