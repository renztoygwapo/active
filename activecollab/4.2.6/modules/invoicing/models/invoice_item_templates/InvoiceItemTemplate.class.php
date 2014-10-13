<?php

  /**
   * InvoiceItemTemplate class
   */
  class InvoiceItemTemplate extends BaseInvoiceItemTemplate implements IRoutingContext {
    
    /**
     * cached value of tax
     * 
     * @var TaxRate
     */
    private $first_tax_rate = false;
    
    /**
     * Return tax rate
     *
     * @return TaxRate
     */
    function getFirstTaxRate() {
      if ($this->first_tax_rate === false) {
        $this->first_tax_rate = DataObjectPool::get('TaxRate', $this->getFirstTaxRateId());
      } // if
      return $this->first_tax_rate;
    } // getFirstTaxRate

    /**
     * Second tax rate cache
     *
     * @var bool
     */
    var $second_tax_rate = false;

    /**
     * Get Second Tax Rate
     *
     * @return TaxRate
     */
    function getSecondTaxRate() {
      if ($this->second_tax_rate === false) {
        $this->second_tax_rate = DataObjectPool::get('TaxRate', $this->getSecondTaxRateId());
      } // if
      return $this->second_tax_rate;
    } // getSecondTaxRate
    
    // validate
    
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
      
      $result['description'] = $this->getDescription(); 
      $result['first_tax_rate'] = $this->getFirstTaxRate();
      $result['second_tax_rate'] = $this->getSecondTaxRate();
      $result['quantity'] = $this->getQuantity();
      $result['unit_cost'] = $this->getUnitCost(); 
      
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
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'invoicing_item_template';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('item_id' => $this->getId());
    } // getRoutingContextParams
    
    
    
    /**
     * Validate model
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('description')) {
        $errors->addError(lang('Description is required'), 'description');
      } // if
      
      if (!$this->getUnitCost()) {
        $this->setUnitCost(0);
      } // if
      
      if (!$this->validatePresenceOf('quantity')) {
        $errors->addError(lang('Quantity is required'), 'quantity');
      } // if
      
      return parent::validate($errors);
    } // validate
    
    // URL-s
    
    /**
     * Get view url
     * 
     * @param void
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('admin_invoicing_items').'#Item_template_'.$this->getId();
    } // getViewUrl
    
    /**
     * Get edit url
     * 
     * @param void
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('admin_invoicing_item_edit', array(
        'item_id' => $this->getId(),
      ));
    } // getEditUrl
    
    /**
     * Get delete url
     * 
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('admin_invoicing_item_delete', array(
        'item_id' => $this->getId(),
      ));
    } // getDeleteUrl
    
  } // InvoiceItemTemplate

?>