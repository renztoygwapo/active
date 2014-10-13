<?php

  /**
   * BaseHomescreenWidget class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseHomescreenWidget extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'homescreen_widgets';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'homescreen_tab_id', 'column_id', 'position', 'raw_additional_properties');
    
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
        return $underscore ? 'homescreen_widget' : 'HomescreenWidget';
      } else {
        return $underscore ? 'homescreen_widgets' : 'HomescreenWidgets';
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
     * Return value of homescreen_tab_id field
     *
     * @return integer
     */
    function getHomescreenTabId() {
      return $this->getFieldValue('homescreen_tab_id');
    } // getHomescreenTabId
    
    /**
     * Set value of homescreen_tab_id field
     *
     * @param integer $value
     * @return integer
     */
    function setHomescreenTabId($value) {
      return $this->setFieldValue('homescreen_tab_id', $value);
    } // setHomescreenTabId

    /**
     * Return value of column_id field
     *
     * @return integer
     */
    function getColumnId() {
      return $this->getFieldValue('column_id');
    } // getColumnId
    
    /**
     * Set value of column_id field
     *
     * @param integer $value
     * @return integer
     */
    function setColumnId($value) {
      return $this->setFieldValue('column_id', $value);
    } // setColumnId

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
          case 'homescreen_tab_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'column_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }