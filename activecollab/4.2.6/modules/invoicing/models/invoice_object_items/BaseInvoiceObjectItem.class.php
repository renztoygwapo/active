<?php

  /**
   * BaseInvoiceObjectItem class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  abstract class BaseInvoiceObjectItem extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'invoice_object_items';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'parent_type', 'parent_id', 'first_tax_rate_id', 'second_tax_rate_id', 'description', 'quantity', 'unit_cost', 'subtotal', 'first_tax', 'second_tax', 'total', 'second_tax_is_enabled', 'second_tax_is_compound', 'position');
    
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
        return $underscore ? 'invoice_object_item' : 'InvoiceObjectItem';
      } else {
        return $underscore ? 'invoice_object_items' : 'InvoiceObjectItems';
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
     * Return value of subtotal field
     *
     * @return float
     */
    function getSubtotal() {
      return $this->getFieldValue('subtotal');
    } // getSubtotal
    
    /**
     * Set value of subtotal field
     *
     * @param float $value
     * @return float
     */
    function setSubtotal($value) {
      return $this->setFieldValue('subtotal', $value);
    } // setSubtotal

    /**
     * Return value of first_tax field
     *
     * @return float
     */
    function getFirstTax() {
      return $this->getFieldValue('first_tax');
    } // getFirstTax
    
    /**
     * Set value of first_tax field
     *
     * @param float $value
     * @return float
     */
    function setFirstTax($value) {
      return $this->setFieldValue('first_tax', $value);
    } // setFirstTax

    /**
     * Return value of second_tax field
     *
     * @return float
     */
    function getSecondTax() {
      return $this->getFieldValue('second_tax');
    } // getSecondTax
    
    /**
     * Set value of second_tax field
     *
     * @param float $value
     * @return float
     */
    function setSecondTax($value) {
      return $this->setFieldValue('second_tax', $value);
    } // setSecondTax

    /**
     * Return value of total field
     *
     * @return float
     */
    function getTotal() {
      return $this->getFieldValue('total');
    } // getTotal
    
    /**
     * Set value of total field
     *
     * @param float $value
     * @return float
     */
    function setTotal($value) {
      return $this->setFieldValue('total', $value);
    } // setTotal

    /**
     * Return value of second_tax_is_enabled field
     *
     * @return boolean
     */
    function getSecondTaxIsEnabled() {
      return $this->getFieldValue('second_tax_is_enabled');
    } // getSecondTaxIsEnabled
    
    /**
     * Set value of second_tax_is_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setSecondTaxIsEnabled($value) {
      return $this->setFieldValue('second_tax_is_enabled', $value);
    } // setSecondTaxIsEnabled

    /**
     * Return value of second_tax_is_compound field
     *
     * @return boolean
     */
    function getSecondTaxIsCompound() {
      return $this->getFieldValue('second_tax_is_compound');
    } // getSecondTaxIsCompound
    
    /**
     * Set value of second_tax_is_compound field
     *
     * @param boolean $value
     * @return boolean
     */
    function setSecondTaxIsCompound($value) {
      return $this->setFieldValue('second_tax_is_compound', $value);
    } // setSecondTaxIsCompound

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
        case 'type':
          return parent::setFieldValue($real_name, (string) $value);
        case 'parent_type':
          return parent::setFieldValue($real_name, (string) $value);
        case 'parent_id':
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
        case 'subtotal':
          return parent::setFieldValue($real_name, (float) $value);
        case 'first_tax':
          return parent::setFieldValue($real_name, (float) $value);
        case 'second_tax':
          return parent::setFieldValue($real_name, (float) $value);
        case 'total':
          return parent::setFieldValue($real_name, (float) $value);
        case 'second_tax_is_enabled':
          return parent::setFieldValue($real_name, (boolean) $value);
        case 'second_tax_is_compound':
          return parent::setFieldValue($real_name, (boolean) $value);
        case 'position':
          return parent::setFieldValue($real_name, (integer) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }