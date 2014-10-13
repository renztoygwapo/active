<?php

  /**
   * BaseJobType class
   *
   * @package ActiveCollab.modules.tracking
   * @subpackage models
   */
  abstract class BaseJobType extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'job_types';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'default_hourly_rate', 'is_default', 'is_active');
    
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
        return $underscore ? 'job_type' : 'JobType';
      } else {
        return $underscore ? 'job_types' : 'JobTypes';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    

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
     * Return value of default_hourly_rate field
     *
     * @return float
     */
    function getDefaultHourlyRate() {
      return $this->getFieldValue('default_hourly_rate');
    } // getDefaultHourlyRate
    
    /**
     * Set value of default_hourly_rate field
     *
     * @param float $value
     * @return float
     */
    function setDefaultHourlyRate($value) {
      return $this->setFieldValue('default_hourly_rate', $value);
    } // setDefaultHourlyRate

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
     * Return value of is_active field
     *
     * @return boolean
     */
    function getIsActive() {
      return $this->getFieldValue('is_active');
    } // getIsActive

    /**
     * Set value of is_active field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsActive($value) {
      return $this->setFieldValue('is_active', $value);
    } // setIsActive

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'default_hourly_rate':
          return parent::setFieldValue($real_name, (float) $value);
        case 'is_default':
          return parent::setFieldValue($real_name, (boolean) $value);
        case 'is_active':
          return parent::setFieldValue($real_name, (boolean) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }