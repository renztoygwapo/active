<?php

  /**
   * TaxRate class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class TaxRate extends BaseTaxRate implements IRoutingContext {

    /**
     * Return true if this particular record is used in external resources (invoice for example)
     *
     * @return boolean
     */
    function isUsed() {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        return InvoiceItems::countByTaxRate($this) > 0;
      } else {
        return false;
      } // if
    } // isUsed
    
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
      
      $result['percentage'] = $this->getPercentage();
      $result['is_default'] = $this->getIsDefault();

      if (!$result['urls']) {
        $result['urls'] = array();
      } // if

      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      $result['urls']['remove_default'] = $this->getRemoveDefaultUrl();
      
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
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'percentage' => $this->getPercentage(),
      );
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
      return 'admin_tax_rate';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('tax_rate_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return verbose percentage
     *
     * @return string
     */
    function getVerbosePercentage() {
      return Globalization::formatNumber($this->getPercentage()) . '%';
    } // getVerbosePercentage
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can update this rate
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit

    /**
     * Returns true if user can make this rate default
     *
     * @param User $user
     * @return boolean
     */
    function canModifyDefaultState(User $user) {
      return $user->isAdministrator();
    } // canMakeDefault
    
    /**
     * Returns true if $user can delete this tax rate
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator() && !$this->isUsed();
    } // canDelete

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Set as default url
     *
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('admin_tax_rate_set_as_default', array('tax_rate_id' => $this->getId()));
    } // getSetAsDefaultUrl

    /**
     * Remove default url
     *
     * @return string
     */
    function getRemoveDefaultUrl() {
      return Router::assemble('admin_tax_rate_remove_default', array('tax_rate_id' => $this->getId()));
    } // getRemoveDefaultUrl


    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Tax Rate name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Tax Rate name is required'), 'name');
      } // if
      
      if($this->validatePresenceOf('percentage')) {
        if($this->getPercentage() > 99.999) {
          $errors->addError(lang('Percentage maximum value is 99.999'), 'percentage');
        } // if
        
        if($this->getPercentage() < 0) {
          $errors->addError(lang('Percentage minimum value is 0'), 'percentage');
        } // if
      } else {
        $errors->addError(lang('Percentage is required'), 'percentage');
      } // if
    } // validate

    /**
     * Save method
     *
     * @return bool|void
     */
    function save() {
      // Override save method so we cannot change tax rate percentage if tax rate is used
      if ($this->isModifiedField('percentage') && $this->isUsed()) {
        $this->revertField('percentage');
      } // if

      return parent::save();
    } // save
    
  }