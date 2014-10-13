<?php

  /**
   * BaseDayOff class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseDayOff extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'day_offs';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'event_date', 'repeat_yearly');
    
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
        return $underscore ? 'day_off' : 'DayOff';
      } else {
        return $underscore ? 'day_offs' : 'DayOffs';
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
     * Return value of event_date field
     *
     * @return DateValue
     */
    function getEventDate() {
      return $this->getFieldValue('event_date');
    } // getEventDate
    
    /**
     * Set value of event_date field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setEventDate($value) {
      return $this->setFieldValue('event_date', $value);
    } // setEventDate

    /**
     * Return value of repeat_yearly field
     *
     * @return boolean
     */
    function getRepeatYearly() {
      return $this->getFieldValue('repeat_yearly');
    } // getRepeatYearly
    
    /**
     * Set value of repeat_yearly field
     *
     * @param boolean $value
     * @return boolean
     */
    function setRepeatYearly($value) {
      return $this->setFieldValue('repeat_yearly', $value);
    } // setRepeatYearly

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
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'event_date':
            return parent::setFieldValue($real_name, dateval($value));
          case 'repeat_yearly':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }