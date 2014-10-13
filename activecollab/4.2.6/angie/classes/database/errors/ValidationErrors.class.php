<?php

  /**
   * Container of multiple validation errors
   *
   * @package angie.library.errors
   */
  class ValidationErrors extends Error {
    
    // Any field
    const ANY_FIELD = '-- any --';
    
    /**
     * Object instance
     *
     * @var DataObject
     */
    private $object;
    
    /**
     * Errors array
     *
     * @var array
     */
    private $errors = array();
  
    /**
     * Construct the FormErrors
     *
     * @param array $errors
     * @param string $message
     */
    function __construct($errors = null, $message = null) {
      if($message === null) {
        $message = 'Validation failed';
      } // if
      
      if(is_array($errors)) {
        foreach($errors as $k => $error) {
          $field = is_numeric($k) ? null : $k;
          if(is_array($error)) {
            foreach($error as $single_error) {
              $this->addError($single_error, $field);
            } // foreach
          } elseif($error) {
            $this->addError($error, $field);
          } // if
        } // if
      } // if
      
      parent::__construct($message, array(
        'errors' => $errors, 
      ));
    } // __construct
    
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
    	$result = array(
    	  'message' => $this->getMessage(),
        'type' => get_class($this),
    	  'field_errors' => array(),
    	);
    	
    	foreach($this->getErrors() as $field => $messages) {
    	  foreach($messages as $message) {
    	    if(!isset($result['field_errors'][$field])) {
    	      $result['field_errors'][$field] = array();
    	    } // if
    	    
    	    $result['field_errors'][$field][] = $message;
    	  } // foreach
    	}  // if
    	
    	if($this->object instanceof DataObject) {
    	  $result['object_class'] = get_class($this->object);
    	  $result['object_fields'] = array();
    	  
    	  foreach($this->object->getFields() as $field) {
    	    $result['object_fields'][$field] = $this->object->getFieldValue($field);
    	  } // foreach
    	} // if
    	
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
      $result = array(
        'message' => $this->getMessage(),
        'type' => get_class($this),
        'field_errors' => array(),
      );

      foreach($this->getErrors() as $field => $messages) {
        foreach($messages as $message) {
          if(!isset($result['field_errors'][$field])) {
            $result['field_errors'][$field] = array();
          } // if

          $result['field_errors'][$field][] = $message;
        } // foreach
      }  // if

      if($this->object instanceof DataObject) {
        $result['object_class'] = get_class($this->object);
        $result['object_fields'] = array();

        foreach($this->object->getFields() as $field) {
          $result['object_fields'][$field] = $this->object->getFieldValue($field);
        } // foreach
      } // if

      return $result;
    } // describeForApi
    
    /**
     * Return parent object instance
     * 
     * @return DataObject
     */
    function getObject() {
      return $this->object;
    } // getObject
    
    /**
     * Set parent object
     * 
     * @param DataObject $value
     * @return DataObject
     */
    function setObject($value) {
      if($value instanceof DataObject || $value === null) {
        $this->object = $value;
      } else {
        throw new InvalidInstanceError('value', $value, 'DataObject');
      } // if
      
      return $this->object;
    } // setObject
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Return number of errors from specific form
     *
     * @return array
     */
    function getErrors() {
      return count($this->errors) ? $this->errors : null;
    } // getErrors
    
    /**
     * Return field errors
     *
     * @param string $field
     * @return array
     */
    function getFieldErrors($field) {
      return isset($this->errors[$field]) ? $this->errors[$field] : null;
    } // getFieldErrors
    
    /**
     * Returns true if there are error messages reported
     *
     * @return boolean
     */
    function hasErrors() {
      return (boolean) count($this->errors);
    } // hasErrors
    
    /**
     * Check if a specific field has reported errors
     *
     * @param string $field
     * @return boolean
     */
    function hasError($field) {
      return isset($this->errors[$field]) && count($this->errors[$field]);
    } // hasError
    
    /**
     * Add error to array
     *
     * @param string $error Error message
     * @param string $field
     */
    function addError($error, $field = ValidationErrors::ANY_FIELD) {
      if(empty($field)) {
        $field = ValidationErrors::ANY_FIELD;
      } // if
      
      if(is_array($this->errors)) {
        $this->errors[$field][] = $error;
      } else {
        $this->errors[$field] = array();
      } // if
    } // addError
    
    /**
     * Returns error messages as string
     * 
     * @return string
     */
    function getErrorsAsString() {
      if($this->hasErrors()) {
        $this_errors = array();
        $errors = $this->getErrors();
        
        foreach ($errors as $error) {
        	$this_errors[] = implode(", ", $error);
        } // foreach
        
        return trim(implode(", ", $this_errors));
      } else {
         return '--';
      } // if
    } // getErrorMessagesAsString
  
  }