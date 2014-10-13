<?php

  /**
   * BaseCalendarEvent class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseCalendarEvent extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'calendar_events';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'parent_type', 'parent_id', 'name', 'starts_on', 'starts_on_time', 'ends_on', 'repeat_event', 'repeat_event_option', 'repeat_until', 'state', 'original_state', 'raw_additional_properties', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'position');
    
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
        return $underscore ? 'calendar_event' : 'CalendarEvent';
      } else {
        return $underscore ? 'calendar_events' : 'CalendarEvents';
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
     * Return value of starts_on field
     *
     * @return DateValue
     */
    function getStartsOn() {
      return $this->getFieldValue('starts_on');
    } // getStartsOn
    
    /**
     * Set value of starts_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setStartsOn($value) {
      return $this->setFieldValue('starts_on', $value);
    } // setStartsOn

    /**
     * Return value of starts_on_time field
     *
     * @return DateTimeValue
     */
    function getStartsOnTime() {
      return $this->getFieldValue('starts_on_time');
    } // getStartsOnTime
    
    /**
     * Set value of starts_on_time field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setStartsOnTime($value) {
      return $this->setFieldValue('starts_on_time', $value);
    } // setStartsOnTime

    /**
     * Return value of ends_on field
     *
     * @return DateValue
     */
    function getEndsOn() {
      return $this->getFieldValue('ends_on');
    } // getEndsOn
    
    /**
     * Set value of ends_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setEndsOn($value) {
      return $this->setFieldValue('ends_on', $value);
    } // setEndsOn

    /**
     * Return value of repeat_event field
     *
     * @return string
     */
    function getRepeatEvent() {
      return $this->getFieldValue('repeat_event');
    } // getRepeatEvent
    
    /**
     * Set value of repeat_event field
     *
     * @param string $value
     * @return string
     */
    function setRepeatEvent($value) {
      return $this->setFieldValue('repeat_event', $value);
    } // setRepeatEvent

    /**
     * Return value of repeat_event_option field
     *
     * @return string
     */
    function getRepeatEventOption() {
      return $this->getFieldValue('repeat_event_option');
    } // getRepeatEventOption
    
    /**
     * Set value of repeat_event_option field
     *
     * @param string $value
     * @return string
     */
    function setRepeatEventOption($value) {
      return $this->setFieldValue('repeat_event_option', $value);
    } // setRepeatEventOption

    /**
     * Return value of repeat_until field
     *
     * @return DateValue
     */
    function getRepeatUntil() {
      return $this->getFieldValue('repeat_until');
    } // getRepeatUntil
    
    /**
     * Set value of repeat_until field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setRepeatUntil($value) {
      return $this->setFieldValue('repeat_until', $value);
    } // setRepeatUntil

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
          case 'parent_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'starts_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'starts_on_time':
            return parent::setFieldValue($real_name, timeval($value));
          case 'ends_on':
            return parent::setFieldValue($real_name, dateval($value));
          case 'repeat_event':
            return parent::setFieldValue($real_name, (string) $value);
          case 'repeat_event_option':
            return parent::setFieldValue($real_name, (string) $value);
          case 'repeat_until':
            return parent::setFieldValue($real_name, dateval($value));
          case 'state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'original_state':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_on':
            return parent::setFieldValue($real_name, datetimeval($value));
          case 'created_by_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'created_by_name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'created_by_email':
            return parent::setFieldValue($real_name, (string) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }