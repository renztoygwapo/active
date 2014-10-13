<?php

  /**
   * TestDataObject class
   *
   * @package angie.tests
   */
  class TestDataObject extends BaseTestDataObject {

    /**
     * Field map, used for map testing
     *
     * @var array
     */
    protected $field_map = array(
      'real_name' => 'name',
      'biography' => 'description',
    );

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError('Name need to be unique', 'name');
        } // if
      } else {
        $errors->addError('Name value is required', 'name');
      } // if
    } // validate
    
  }