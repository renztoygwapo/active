<?php

  /**
   * BaseReminder class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseReminder extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'reminders';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'parent_type', 'parent_id', 'send_to', 'send_on', 'sent_on', 'comment', 'selected_user_id', 'created_by_id', 'created_by_name', 'created_by_email', 'created_on', 'dismissed_on');
    
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
        return $underscore ? 'reminder' : 'Reminder';
      } else {
        return $underscore ? 'reminders' : 'Reminders';
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
     * Return value of send_to field
     *
     * @return string
     */
    function getSendTo() {
      return $this->getFieldValue('send_to');
    } // getSendTo
    
    /**
     * Set value of send_to field
     *
     * @param string $value
     * @return string
     */
    function setSendTo($value) {
      return $this->setFieldValue('send_to', $value);
    } // setSendTo

    /**
     * Return value of send_on field
     *
     * @return DateTimeValue
     */
    function getSendOn() {
      return $this->getFieldValue('send_on');
    } // getSendOn
    
    /**
     * Set value of send_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setSendOn($value) {
      return $this->setFieldValue('send_on', $value);
    } // setSendOn

    /**
     * Return value of sent_on field
     *
     * @return DateTimeValue
     */
    function getSentOn() {
      return $this->getFieldValue('sent_on');
    } // getSentOn
    
    /**
     * Set value of sent_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setSentOn($value) {
      return $this->setFieldValue('sent_on', $value);
    } // setSentOn

    /**
     * Return value of comment field
     *
     * @return string
     */
    function getComment() {
      return $this->getFieldValue('comment');
    } // getComment
    
    /**
     * Set value of comment field
     *
     * @param string $value
     * @return string
     */
    function setComment($value) {
      return $this->setFieldValue('comment', $value);
    } // setComment

    /**
     * Return value of selected_user_id field
     *
     * @return integer
     */
    function getSelectedUserId() {
      return $this->getFieldValue('selected_user_id');
    } // getSelectedUserId
    
    /**
     * Set value of selected_user_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSelectedUserId($value) {
      return $this->setFieldValue('selected_user_id', $value);
    } // setSelectedUserId

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
     * Return value of dismissed_on field
     *
     * @return DateTimeValue
     */
    function getDismissedOn() {
      return $this->getFieldValue('dismissed_on');
    } // getDismissedOn
    
    /**
     * Set value of dismissed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDismissedOn($value) {
      return $this->setFieldValue('dismissed_on', $value);
    } // setDismissedOn

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
          case 'send_to':
            return parent::setFieldValue($real_name, (string) $value);
          case 'send_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'sent_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'comment':
            return parent::setFieldValue($real_name, (string) $value);
          case 'selected_user_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'dismissed_on':
            return parent::setFieldValue($real_name, datetimeval($value));
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }