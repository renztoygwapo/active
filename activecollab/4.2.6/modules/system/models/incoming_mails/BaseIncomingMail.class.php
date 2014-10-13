<?php

  /**
   * BaseIncomingMail class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseIncomingMail extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'incoming_mails';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'incoming_mailbox_id', 'parent_type', 'parent_id', 'is_replay_to_notification', 'subject', 'body', 'to_email', 'cc_to', 'bcc_to', 'reply_to', 'priority', 'additional_data', 'headers', 'status', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'raw_additional_properties');
    
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
        return $underscore ? 'incoming_mail' : 'IncomingMail';
      } else {
        return $underscore ? 'incoming_mails' : 'IncomingMails';
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
     * Return value of incoming_mailbox_id field
     *
     * @return integer
     */
    function getIncomingMailboxId() {
      return $this->getFieldValue('incoming_mailbox_id');
    } // getIncomingMailboxId
    
    /**
     * Set value of incoming_mailbox_id field
     *
     * @param integer $value
     * @return integer
     */
    function setIncomingMailboxId($value) {
      return $this->setFieldValue('incoming_mailbox_id', $value);
    } // setIncomingMailboxId

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
     * Return value of is_replay_to_notification field
     *
     * @return boolean
     */
    function getIsReplayToNotification() {
      return $this->getFieldValue('is_replay_to_notification');
    } // getIsReplayToNotification
    
    /**
     * Set value of is_replay_to_notification field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsReplayToNotification($value) {
      return $this->setFieldValue('is_replay_to_notification', $value);
    } // setIsReplayToNotification

    /**
     * Return value of subject field
     *
     * @return string
     */
    function getSubject() {
      return $this->getFieldValue('subject');
    } // getSubject
    
    /**
     * Set value of subject field
     *
     * @param string $value
     * @return string
     */
    function setSubject($value) {
      return $this->setFieldValue('subject', $value);
    } // setSubject

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
     * Return value of cc_to field
     *
     * @return string
     */
    function getCcTo() {
      return $this->getFieldValue('cc_to');
    } // getCcTo
    
    /**
     * Set value of cc_to field
     *
     * @param string $value
     * @return string
     */
    function setCcTo($value) {
      return $this->setFieldValue('cc_to', $value);
    } // setCcTo

    /**
     * Return value of bcc_to field
     *
     * @return string
     */
    function getBccTo() {
      return $this->getFieldValue('bcc_to');
    } // getBccTo
    
    /**
     * Set value of bcc_to field
     *
     * @param string $value
     * @return string
     */
    function setBccTo($value) {
      return $this->setFieldValue('bcc_to', $value);
    } // setBccTo

    /**
     * Return value of reply_to field
     *
     * @return string
     */
    function getReplyTo() {
      return $this->getFieldValue('reply_to');
    } // getReplyTo
    
    /**
     * Set value of reply_to field
     *
     * @param string $value
     * @return string
     */
    function setReplyTo($value) {
      return $this->setFieldValue('reply_to', $value);
    } // setReplyTo

    /**
     * Return value of priority field
     *
     * @return string
     */
    function getPriority() {
      return $this->getFieldValue('priority');
    } // getPriority
    
    /**
     * Set value of priority field
     *
     * @param string $value
     * @return string
     */
    function setPriority($value) {
      return $this->setFieldValue('priority', $value);
    } // setPriority

    /**
     * Return value of additional_data field
     *
     * @return string
     */
    function getAdditionalData() {
      return $this->getFieldValue('additional_data');
    } // getAdditionalData
    
    /**
     * Set value of additional_data field
     *
     * @param string $value
     * @return string
     */
    function setAdditionalData($value) {
      return $this->setFieldValue('additional_data', $value);
    } // setAdditionalData

    /**
     * Return value of headers field
     *
     * @return string
     */
    function getHeaders() {
      return $this->getFieldValue('headers');
    } // getHeaders
    
    /**
     * Set value of headers field
     *
     * @param string $value
     * @return string
     */
    function setHeaders($value) {
      return $this->setFieldValue('headers', $value);
    } // setHeaders

    /**
     * Return value of status field
     *
     * @return string
     */
    function getStatus() {
      return $this->getFieldValue('status');
    } // getStatus
    
    /**
     * Set value of status field
     *
     * @param string $value
     * @return string
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    } // setStatus

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
          case 'incoming_mailbox_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'is_replay_to_notification':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'subject':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'to_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'cc_to':
            return parent::setFieldValue($real_name, (string) $value);
          case 'bcc_to':
            return parent::setFieldValue($real_name, (string) $value);
          case 'reply_to':
            return parent::setFieldValue($real_name, (string) $value);
          case 'priority':
            return parent::setFieldValue($real_name, (string) $value);
          case 'additional_data':
            return parent::setFieldValue($real_name, (string) $value);
          case 'headers':
            return parent::setFieldValue($real_name, (string) $value);
          case 'status':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }