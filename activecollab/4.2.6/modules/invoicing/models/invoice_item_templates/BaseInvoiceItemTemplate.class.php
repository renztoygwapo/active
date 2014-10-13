<?php

  /**
   * BaseInvoiceItemTemplate class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  abstract class BaseInvoiceItemTemplate extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'invoice_item_templates';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'first_tax_rate_id', 'second_tax_rate_id', 'description', 'quantity', 'unit_cost', 'position');
    
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
        return $underscore ? 'invoice_item_template' : 'InvoiceItemTemplate';
      } else {
        return $underscore ? 'invoice_item_templates' : 'InvoiceItemTemplates';
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
     * Return value of first_tax_rate_id field
     *
     * @return integer
     */
    function getFirstTaxRateId() {
      return $this->getFieldValue('first_tax_rate_id');
    } // getFirstTaxRateId
    
    /**
     * Set value of first_tax_rate_id field
     *
     * @param integer $value
     * @return integer
     */
    function setFirstTaxRateId($value) {
      return $this->setFieldValue('first_tax_rate_id', $value);
    } // setFirstTaxRateId

    /**
     * Return value of second_tax_rate_id field
     *
     * @return integer
     */
    function getSecondTaxRateId() {
      return $this->getFieldValue('second_tax_rate_id');
    } // getSecondTaxRateId
    
    /**
     * Set value of second_tax_rate_id field
     *
     * @param integer $value
     * @return integer
     */
    function setSecondTaxRateId($value) {
      return $this->setFieldValue('second_tax_rate_id', $value);
    } // setSecondTaxRateId

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
     * Return value of quantity field
     *
     * @return float
     */
    function getQuantity() {
      return $this->getFieldValue('quantity');
    } // getQuantity
    
    /**
     * Set value of quantity field
     *
     * @param float $value
     * @return float
     */
    function setQuantity($value) {
      return $this->setFieldValue('quantity', $value);
    } // setQuantity

    /**
     * Return value of unit_cost field
     *
     * @return float
     */
    function getUnitCost() {
      return $this->getFieldValue('unit_cost');
    } // getUnitCost
    
    /**
     * Set value of unit_cost field
     *
     * @param float $value
     * @return float
     */
    function setUnitCost($value) {
      return $this->setFieldValue('unit_cost', $value);
    } // setUnitCost

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
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'first_tax_rate_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'second_tax_rate_id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'description':
          return parent::setFieldValue($real_name, (string) $value);
        case 'quantity':
          return parent::setFieldValue($real_name, (float) $value);
        case 'unit_cost':
          return parent::setFieldValue($real_name, (float) $value);
        case 'position':
          return parent::setFieldValue($real_name, (integer) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }