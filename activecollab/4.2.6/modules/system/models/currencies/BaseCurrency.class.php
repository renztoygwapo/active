<?php

  /**
   * BaseCurrency class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseCurrency extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'currencies';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'code', 'decimal_spaces', 'decimal_rounding', 'is_default');
    
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
        return $underscore ? 'currency' : 'Currency';
      } else {
        return $underscore ? 'currencies' : 'Currencies';
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
     * Return value of decimal_spaces field
     *
     * @return integer
     */
    function getDecimalSpaces() {
      return $this->getFieldValue('decimal_spaces');
    } // getDecimalSpaces
    
    /**
     * Set value of decimal_spaces field
     *
     * @param integer $value
     * @return integer
     */
    function setDecimalSpaces($value) {
      return $this->setFieldValue('decimal_spaces', $value);
    } // setDecimalSpaces

    /**
     * Return value of decimal_rounding field
     *
     * @return float
     */
    function getDecimalRounding() {
      return $this->getFieldValue('decimal_rounding');
    } // getDecimalRounding
    
    /**
     * Set value of decimal_rounding field
     *
     * @param float $value
     * @return float
     */
    function setDecimalRounding($value) {
      return $this->setFieldValue('decimal_rounding', $value);
    } // setDecimalRounding

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
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'code':
            return parent::setFieldValue($real_name, (string) $value);
          case 'decimal_spaces':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'decimal_rounding':
            return parent::setFieldValue($real_name, (float) $value);
          case 'is_default':
            return parent::setFieldValue($real_name, (boolean) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }