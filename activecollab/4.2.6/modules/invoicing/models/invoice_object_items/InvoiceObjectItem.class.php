<?php

  /**
   * InvoiceObjectItem class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceObjectItem extends BaseInvoiceObjectItem {

    // ---------------------------------------------------
    //  SETTERS AND GETTERS
    // ---------------------------------------------------

    /**
     * Get parent object
     *
     * @return ApplicationObject
     */
    function getParent() {
      return DataObjectPool::get($this->getParentType(), $this->getParentId());
    } // getParent

    /**
     * Decimal spaces in this object
     *
     * @var int
     */
    protected $decimal_precision = false;

    /**
     * Get decimal scale
     *
     * @return int
     */
    function getDecimalPrecision() {
      if ($this->decimal_precision === false) {
        $parent = $this->getParent();

        if ($parent && $parent instanceof DataObject && !$parent->isNew()) {
          $currency = $parent->getCurrency();
          if ($currency instanceof Currency) {
            $this->decimal_precision = $currency->getDecimalSpaces();
          } // if
        } // if

        if ($this->decimal_precision === false) {
          $this->decimal_precision = 2;
        } // if
      } // if

      return $this->decimal_precision;
    } // getDecimalSpaces

    // ---------------------------------------------------
    //  SETTERS AND GETTERS OF FIELDS
    // ---------------------------------------------------

    protected $roundable_fields = array(
      'quantity', 'unit_cost', 'subtotal', 'first_tax', 'second_tax', 'total'
    );

    /**
     * Get Field Value
     *
     * @param string $field
     * @param null $default
     * @return float|mixed
     */
    function getFieldValue($field, $default = null) {
      if (in_array($field, $this->roundable_fields)) {
        return round(parent::getFieldValue($field, $default), $this->getDecimalPrecision());
      } else {
        return parent::getFieldValue($field, $default);
      } // if
    } // getFieldValue

    /**
     * Set field value
     *
     * @return float|mixed
     */
    function setFieldValue($name, $value) {
      if (in_array($name, $this->roundable_fields)) {
        return parent::setFieldValue($name, round($value, $this->getDecimalPrecision()));
      } else {
        return parent::setFieldValue($name, $value);
      } // if
    } // setFieldValue

    /**
     * Get Second Tax is Compound
     *
     * @return boolean
     */
    function getSecondTaxIsCompound() {
      if (!$this->getSecondTaxIsEnabled()) {
        return false;
      } // if

      return parent::getSecondTaxIsCompound();
    } // getSecondTaxIsCompound

    /**
     * Get Formatted description
     *
     * @return string
     */
    function getFormattedDescription() {
      return nl2br(clean($this->getDescription()));
    } // getFormattedDescription

    /**
     * Cached first tax rate instance
     *
     * @var TaxRate
     */
    var $first_tax_rate = false;

    /**
     * Return related first tax rate
     *
     * @return TaxRate
     */
    function getFirstTaxRate() {
      if ($this->first_tax_rate === false) {
        $this->first_tax_rate = $this->getFirstTaxRateId() ? DataObjectPool::get('TaxRate', $this->getFirstTaxRateId()) : null;
      } // if

      return $this->first_tax_rate;
    } // getFirstTaxRate

    /**
     * Cached related second tax rate
     *
     * @var TaxRate
     */
    var $second_tax_rate = false;

    /**
     * Get Second Tax Rate
     *
     * @return TaxRate
     */
    function getSecondTaxRate() {
      if ($this->second_tax_rate === false) {
        $this->second_tax_rate = $this->getSecondTaxRateId() ? DataObjectPool::get('TaxRate', $this->getSecondTaxRateId()) : null;
      } // if

      return $this->second_tax_rate;
    } // getSecondTaxRate

    /**
     * Return first tax rate name string
     *
     * @return string
     */
    function getFirstTaxRateName() {
      return $this->getFirstTaxRate() instanceof TaxRate ? $this->getFirstTaxRate()->getName() : '--';
    } // getFirstTaxRateName

    /**
     * Return second tax rate name string
     *
     * @return string
     */
    function getSecondTaxRateName() {
      return $this->getSecondTaxRate() instanceof TaxRate ? $this->getSecondTaxRate()->getName() : '--';
    } // getSecondTaxRateName

    /**
     * Return first tax rate value string
     *
     * @return string
     */
    function getFirstTaxRatePercentageVerbose() {
      return $this->getFirstTaxRate() instanceof TaxRate ? $this->getFirstTaxRate()->getVerbosePercentage() : '-';
    } // getFirstTaxRatePercentage

    /**
     * Return second tax rate percentage verbose
     *
     * @return string
     */
    function getSecondTaxRatePercentageVerbose() {
      return $this->getSecondTaxRate() instanceof TaxRate ? $this->getSecondTaxRate()->getVerbosePercentage() : '-';
    } // getSecondTaxRatePercentageVerbose

    /**
     * Recalculate Cached Fields
     */
    function recalculate() {
      $this->setSubtotal($this->getUnitCost() * $this->getQuantity());
      $this->setFirstTax($this->getFirstTaxRate() instanceof TaxRate ? $this->getSubTotal() * $this->getFirstTaxRate()->getPercentage() / 100 : 0);

      if ($this->getSecondTaxIsEnabled()) {
        if ($this->getSecondTaxIsCompound()) {
          $this->setSecondTax($this->getSecondTaxRate() instanceof TaxRate ? ($this->getSubtotal() + $this->getFirstTax()) * $this->getSecondTaxRate()->getPercentage() / 100 : 0);
        } else {
          $this->setSecondTax($this->getSecondTaxRate() instanceof TaxRate ? $this->getSubtotal() * $this->getSecondTaxRate()->getPercentage() / 100 : 0);
        } // if
      } // if

      $this->setTotal($this->getSubTotal() + $this->getFirstTax() + $this->getSecondTax());
    } // recalculate

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      unset($result['name']);

      $result['description'] = $this->getDescription();
      $result['formatted_description'] = $this->getFormattedDescription();
      $result['unit_cost'] = $this->getUnitCost();
      $result['quantity'] = $this->getQuantity();
      $result['subtotal'] = $this->getSubTotal();
      $result['total'] = $this->getTotal();
      $result['position'] = $this->getPosition();

      // first tax
      $result['first_tax'] = array(
        'id'                  => $this->getFirstTaxRateId(),
        'name'                => $this->getFirstTaxRateName(),
        'value'               => $this->getFirstTax(),
        'verbose_percentage'  => $this->getFirstTaxRatePercentageVerbose()
      );

      // second tax
      $result['second_tax'] = array(
        'id'                  => $this->getSecondTaxRateId(),
        'name'                => $this->getSecondTaxRateName(),
        'value'               => $this->getSecondTax(),
        'verbose_percentage'  => $this->getSecondTaxRatePercentageVerbose()
      );

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);
      unset($result['name']);

      $result['description'] = $this->getDescription();
      $result['formatted_description'] = $this->getFormattedDescription();
      $result['unit_cost'] = $this->getUnitCost();
      $result['quantity'] = $this->getQuantity();
      $result['subtotal'] = $this->getSubTotal();
      $result['total'] = $this->getTotal();
      $result['position'] = $this->getPosition();

      // first tax
      $result['first_tax'] = array(
        'id'                  => $this->getFirstTaxRateId(),
        'name'                => $this->getFirstTaxRateName(),
        'value'               => $this->getFirstTax(),
        'verbose_percentage'  => $this->getFirstTaxRatePercentageVerbose()
      );

      // second tax
      $result['second_tax'] = array(
        'id'                  => $this->getSecondTaxRateId(),
        'name'                => $this->getSecondTaxRateName(),
        'value'               => $this->getSecondTax(),
        'verbose_percentage'  => $this->getSecondTaxRatePercentageVerbose()
      );

      return $result;
    } // describeForApi

    /**
     * Save Invoice Object Item
     *
     * @return bool
     */
    function save() {
      $this->recalculate();
      return parent::save();
    } // save

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('description')) {
        $errors->addError(lang('Item description is required'), 'name');
      } // if

      if(!$this->validatePresenceOf('quantity')) {
        $errors->addError(lang('Quantity is required'), 'quantity');
      } // if

      if (!$this->getUnitCost()) {
        $this->setUnitCost(0);
      } // if

      return parent::validate($errors);
    } // validate
    
  }