<?php

  /**
   * BaseOutgoingMessage class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseOutgoingMessage extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'outgoing_messages';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'decorator', 'sender_id', 'sender_name', 'sender_email', 'recipient_id', 'recipient_name', 'recipient_email', 'subject', 'body', 'context_id', 'code', 'mailing_method', 'created_on', 'send_retries', 'last_send_error');
    
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
        return $underscore ? 'outgoing_message' : 'OutgoingMessage';
      } else {
        return $underscore ? 'outgoing_messages' : 'OutgoingMessages';
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
     * Return value of decorator field
     *
     * @return string
     */
    function getDecorator() {
      return $this->getFieldValue('decorator');
    } // getDecorator
    
    /**
     * Set value of decorator field
     *
     * @param string $value
     * @return string
     */
    function setDecorator($value) {
      return $this->setFieldValue('decorator', $value);
    } // setDecorator

    /**
     * Return value of sender_id field
     *
     * @return integer
     */
    function getSenderId() {
      return $this->getFieldValue('sender_id');
    } // getSenderId
    
    /**
     * Set value of sender_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSenderId($value) {
      return $this->setFieldValue('sender_id', $value);
    } // setSenderId

    /**
     * Return value of sender_name field
     *
     * @return string
     */
    function getSenderName() {
      return $this->getFieldValue('sender_name');
    } // getSenderName
    
    /**
     * Set value of sender_name field
     *
     * @param string $value
     * @return string
     */
    function setSenderName($value) {
      return $this->setFieldValue('sender_name', $value);
    } // setSenderName

    /**
     * Return value of sender_email field
     *
     * @return string
     */
    function getSenderEmail() {
      return $this->getFieldValue('sender_email');
    } // getSenderEmail
    
    /**
     * Set value of sender_email field
     *
     * @param string $value
     * @return string
     */
    function setSenderEmail($value) {
      return $this->setFieldValue('sender_email', $value);
    } // setSenderEmail

    /**
     * Return value of recipient_id field
     *
     * @return integer
     */
    function getRecipientId() {
      return $this->getFieldValue('recipient_id');
    } // getRecipientId
    
    /**
     * Set value of recipient_id field
     *
     * @param integer $value
     * @return integer
     */
    function setRecipientId($value) {
      return $this->setFieldValue('recipient_id', $value);
    } // setRecipientId

    /**
     * Return value of recipient_name field
     *
     * @return string
     */
    function getRecipientName() {
      return $this->getFieldValue('recipient_name');
    } // getRecipientName
    
    /**
     * Set value of recipient_name field
     *
     * @param string $value
     * @return string
     */
    function setRecipientName($value) {
      return $this->setFieldValue('recipient_name', $value);
    } // setRecipientName

    /**
     * Return value of recipient_email field
     *
     * @return string
     */
    function getRecipientEmail() {
      return $this->getFieldValue('recipient_email');
    } // getRecipientEmail
    
    /**
     * Set value of recipient_email field
     *
     * @param string $value
     * @return string
     */
    function setRecipientEmail($value) {
      return $this->setFieldValue('recipient_email', $value);
    } // setRecipientEmail

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
     * Return value of context_id field
     *
     * @return string
     */
    function getContextId() {
      return $this->getFieldValue('context_id');
    } // getContextId
    
    /**
     * Set value of context_id field
     *
     * @param string $value
     * @return string
     */
    function setContextId($value) {
      return $this->setFieldValue('context_id', $value);
    } // setContextId

    /**
     * Return value of code field
     *
     * @return string
     */
    function getCode() {
      return $this->getFieldValue('code');
    } // getCode
    
    /**
     * Set value of code field
     *
     * @param string $value
     * @return string
     */
    function setCode($value) {
      return $this->setFieldValue('code', $value);
    } // setCode

    /**
     * Return value of mailing_method field
     *
     * @return string
     */
    function getMailingMethod() {
      return $this->getFieldValue('mailing_method');
    } // getMailingMethod
    
    /**
     * Set value of mailing_method field
     *
     * @param string $value
     * @return string
     */
    function setMailingMethod($value) {
      return $this->setFieldValue('mailing_method', $value);
    } // setMailingMethod

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
     * Return value of send_retries field
     *
     * @return integer
     */
    function getSendRetries() {
      return $this->getFieldValue('send_retries');
    } // getSendRetries
    
    /**
     * Set value of send_retries field
     *
     * @param integer $value
     * @return integer
     */
    function setSendRetries($value) {
      return $this->setFieldValue('send_retries', $value);
    } // setSendRetries

    /**
     * Return value of last_send_error field
     *
     * @return string
     */
    function getLastSendError() {
      return $this->getFieldValue('last_send_error');
    } // getLastSendError
    
    /**
     * Set value of last_send_error field
     *
     * @param string $value
     * @return string
     */
    function setLastSendError($value) {
      return $this->setFieldValue('last_send_error', $value);
    } // setLastSendError

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
          case 'decorator':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sender_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'sender_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sender_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'recipient_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'recipient_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'recipient_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'subject':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'context_id':
            return parent::setFieldValue($real_name, (string) $value);
          case 'code':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mailing_method':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'send_retries':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'last_send_error':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }