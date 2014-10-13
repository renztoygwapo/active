<?php

  /**
   * Framework level currency implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage models
   */
  class FwCurrency extends BaseCurrency implements IRoutingContext {
    
    /**
     * Return properly formatted value
     * 
     * @param float $value
     * @param Language $language
     * @param boolean $with_currency_code
     * @return string
     */
    function format($value, $language = null, $with_currency_code = false) {
      return Globalization::formatMoney($value, $this, $language, $with_currency_code);
    } // format
    
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

      $result['code'] = $this->getCode();
      $result['is_default'] = $this->getIsDefault();
      $result['decimal_spaces'] = $this->getDecimalSpaces();
      $result['decimal_rounding'] = $this->getDecimalRounding();
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      
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
        'id'                => $this->getId(),
        'name'              => $this->getName(),
        'code'              => $this->getCode(),
        'decimal_spaces'    => $this->getDecimalSpaces(),
        'decimal_rounding'  => $this->getDecimalRounding(),
        'is_default'        => $this->getIsDefault(),
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
      return 'admin_currency';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('currency_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see currency details
     * 
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isAdministrator();
    } // canView
    
    /**
     * Check if $user can edit this currency
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this currency
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator() && !$this->getIsDefault();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return set as default currency URL
     *
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('admin_currency_set_as_default', array('currency_id' => $this->getId()));
    } // getSetAsDefaultUrl
    
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
          $errors->addError(lang('Currency name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Currency name is required'), 'name');
      } // if
      
      if($this->validatePresenceOf('code')) {
        if(!$this->validateUniquenessOf('code')) {
          $errors->addError(lang('Currency code needs to be unique'), 'code');
        } // if
      } else {
        $errors->addError(lang('Currency code is required'), 'code');
      } // if
    } // validate

    /**
     * Save a currency
     */
    function save() {
      $save = parent::save();

      AngieApplication::cache()->remove("currencies_id_name_map");
      AngieApplication::cache()->remove("currencies_id_details_map");

      return $save;
    } // save
    
  }