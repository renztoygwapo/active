<?php

  /**
   * BaseIncomingMailFilter class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseIncomingMailFilter extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'incoming_mail_filters';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'name', 'description', 'subject', 'body', 'priority', 'attachments', 'sender', 'to_email', 'mailbox_id', 'action_name', 'action_parameters', 'position', 'is_enabled', 'is_default');
    
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
        return $underscore ? 'incoming_mail_filter' : 'IncomingMailFilter';
      } else {
        return $underscore ? 'incoming_mail_filters' : 'IncomingMailFilters';
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
     * Return value of description field
     *
     * @return string
     */
    function getDescription() {
      return $this->getFieldValue('description');
    } // getDescription
    
    /**
     * Set value of description field
     *
     * @param string $value
     * @return string
     */
    function setDescription($value) {
      return $this->setFieldValue('description', $value);
    } // setDescription

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
     * Return value of attachments field
     *
     * @return string
     */
    function getAttachments() {
      return $this->getFieldValue('attachments');
    } // getAttachments
    
    /**
     * Set value of attachments field
     *
     * @param string $value
     * @return string
     */
    function setAttachments($value) {
      return $this->setFieldValue('attachments', $value);
    } // setAttachments

    /**
     * Return value of sender field
     *
     * @return string
     */
    function getSender() {
      return $this->getFieldValue('sender');
    } // getSender
    
    /**
     * Set value of sender field
     *
     * @param string $value
     * @return string
     */
    function setSender($value) {
      return $this->setFieldValue('sender', $value);
    } // setSender

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
     * Return value of mailbox_id field
     *
     * @return string
     */
    function getMailboxId() {
      return $this->getFieldValue('mailbox_id');
    } // getMailboxId
    
    /**
     * Set value of mailbox_id field
     *
     * @param string $value
     * @return string
     */
    function setMailboxId($value) {
      return $this->setFieldValue('mailbox_id', $value);
    } // setMailboxId

    /**
     * Return value of action_name field
     *
     * @return string
     */
    function getActionName() {
      return $this->getFieldValue('action_name');
    } // getActionName
    
    /**
     * Set value of action_name field
     *
     * @param string $value
     * @return string
     */
    function setActionName($value) {
      return $this->setFieldValue('action_name', $value);
    } // setActionName

    /**
     * Return value of action_parameters field
     *
     * @return string
     */
    function getActionParameters() {
      return $this->getFieldValue('action_parameters');
    } // getActionParameters
    
    /**
     * Set value of action_parameters field
     *
     * @param string $value
     * @return string
     */
    function setActionParameters($value) {
      return $this->setFieldValue('action_parameters', $value);
    } // setActionParameters

    /**
     * Return value of position field
     *
     * @return integer
     */
    function getPosition() {
      return $this->getFieldValue('position');
    } // getPosition
    
    /**
     * Set value of position field
     *
     * @param integer $value
     * @return integer
     */
    function setPosition($value) {
      return $this->setFieldValue('position', $value);
    } // setPosition

    /**
     * Return value of is_enabled field
     *
     * @return boolean
     */
    function getIsEnabled() {
      return $this->getFieldValue('is_enabled');
    } // getIsEnabled
    
    /**
     * Set value of is_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsEnabled($value) {
      return $this->setFieldValue('is_enabled', $value);
    } // setIsEnabled

    /**
     * Return value of is_default field
     *
     * @return boolean
     */
    function getIsDefault() {
      return $this->getFieldValue('is_default');
    } // getIsDefault
    
    /**
     * Set value of is_default field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDefault($value) {
      return $this->setFieldValue('is_default', $value);
    } // setIsDefault

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
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'description':
            return parent::setFieldValue($real_name, (string) $value);
          case 'subject':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'priority':
            return parent::setFieldValue($real_name, (string) $value);
          case 'attachments':
            return parent::setFieldValue($real_name, (string) $value);
          case 'sender':
            return parent::setFieldValue($real_name, (string) $value);
          case 'to_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mailbox_id':
            return parent::setFieldValue($real_name, (string) $value);
          case 'action_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'action_parameters':
            return parent::setFieldValue($real_name, (string) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'is_default':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }