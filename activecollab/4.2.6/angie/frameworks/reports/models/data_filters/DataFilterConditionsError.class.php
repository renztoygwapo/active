<?php

  /**
   * Exception that is thrown when filter can't prepare a particular error because condition is not possible
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class DataFilterConditionsError extends Error {
    
    /**
     * Construct new assignment filter conditions error
     * 
     * @param string $filter_name
     * @param string $filter_value
     * @param mixed $filter_data
     * @param string $message
     */
    function __construct($filter_name, $filter_value, $filter_data = null, $message = null) {
      if($message === null) {
        $message = 'Can not prepare filter conditions';
      } // if
      
      parent::__construct($message, array(
        'filter_name' => $filter_name, 
        'filter_value' => $filter_value, 
        'filter_data' => $filter_data, 
      ));
    } // __construct
    
  }