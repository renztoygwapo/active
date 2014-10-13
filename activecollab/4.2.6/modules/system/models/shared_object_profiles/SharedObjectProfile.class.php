<?php

  /**
   * SharedObjectProfile class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SharedObjectProfile extends BaseSharedObjectProfile {
    
    /**
     * Cached is expired value
     *
     * @var boolean
     */
    protected $is_expired = null;
    
    /**
     * Returns true if this profile expired
     * 
     * @return boolean
     */
    function isExpired() {
      if($this->is_expired === null) {
        if($this->getExpiresOn() instanceof DateTimeValue) {
          $this->is_expired = $this->getExpiresOn()->getTimestamp() <= DateTimeValue::now()->getTimestamp();
        } else {
          $this->is_expired = false;
        } // if
      } // if
      
      return $this->is_expired;
    } // isExpired
    
    /**
     * Set object attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('expires_on', $attributes)) {
        $this->is_expired = null;
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Set expires on value
     * 
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setExpiresOn($value) {
      $this->is_expired = null;
      
      return parent::setExpiresOn($value);
    } // setExpiresOn
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('parent_type') && $this->validatePresenceOf('parent_id')) {
        if(!$this->validateUniquenessOf('parent_type', 'parent_id')) {
          $errors->addError(lang('Selected object is already shared'), 'parent');
        } // if
      } else {
        $errors->addError(lang('Shared object is required'), 'parent');
      } // if
      
      if(!$this->validatePresenceOf('sharing_context')) {
        $errors->addError(lang('Sharing context is required'), 'sharing_context');
      } // if
      
      if(!$this->validatePresenceOf('sharing_code')) {
        $errors->addError(lang('Sharing code is required'), 'sharing_code');
      } // if
    } // validate
    
  }