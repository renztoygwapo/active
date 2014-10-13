<?php

  /**
   * Framework level day off model implemetaiton
   *
   * @package angie.frameworks.globalization
   * @subpackage models
   */
  class FwDayOff extends BaseDayOff {

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
      return array(
        'name' => $this->getName(),
        'date' => $this->getEventDate(),
        'repeat_yearly' => $this->getRepeatYearly(),
      );
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
        'name' => $this->getName(),
        'date' => $this->getEventDate(),
        'repeat_yearly' => $this->getRepeatYearly(),
      );
    } // describe
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name', 'event_date')) {
          $errors->addError(lang('Event already specified for given date'), 'name');
        } // if
      } else {
        $errors->addError(lang('Name is required'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('event_date')) {
        $errors->addError(lang('Event date is required'), 'event_date');
      } // if
    } // validate
    
  }