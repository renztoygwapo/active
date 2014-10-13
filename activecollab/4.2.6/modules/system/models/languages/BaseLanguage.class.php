<?php

  /**
   * BaseLanguage class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseLanguage extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'languages';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'locale', 'decimal_separator', 'thousands_separator', 'last_updated_on');
    
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
        return $underscore ? 'language' : 'Language';
      } else {
        return $underscore ? 'languages' : 'Languages';
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
     * Return value of locale field
     *
     * @return string
     */
    function getLocale() {
      return $this->getFieldValue('locale');
    } // getLocale
    
    /**
     * Set value of locale field
     *
     * @param string $value
     * @return string
     */
    function setLocale($value) {
      return $this->setFieldValue('locale', $value);
    } // setLocale

    /**
     * Return value of decimal_separator field
     *
     * @return string
     */
    function getDecimalSeparator() {
      return $this->getFieldValue('decimal_separator');
    } // getDecimalSeparator
    
    /**
     * Set value of decimal_separator field
     *
     * @param string $value
     * @return string
     */
    function setDecimalSeparator($value) {
      return $this->setFieldValue('decimal_separator', $value);
    } // setDecimalSeparator

    /**
     * Return value of thousands_separator field
     *
     * @return string
     */
    function getThousandsSeparator() {
      return $this->getFieldValue('thousands_separator');
    } // getThousandsSeparator
    
    /**
     * Set value of thousands_separator field
     *
     * @param string $value
     * @return string
     */
    function setThousandsSeparator($value) {
      return $this->setFieldValue('thousands_separator', $value);
    } // setThousandsSeparator

    /**
     * Return value of last_updated_on field
     *
     * @return DateTimeValue
     */
    function getLastUpdatedOn() {
      return $this->getFieldValue('last_updated_on');
    } // getLastUpdatedOn
    
    /**
     * Set value of last_updated_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastUpdatedOn($value) {
      return $this->setFieldValue('last_updated_on', $value);
    } // setLastUpdatedOn

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
          case 'locale':
            return parent::setFieldValue($real_name, (string) $value);
          case 'decimal_separator':
            return parent::setFieldValue($real_name, (string) $value);
          case 'thousands_separator':
            return parent::setFieldValue($real_name, (string) $value);
          case 'last_updated_on':
            return parent::setFieldValue($real_name, datetimeval($value));
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }