<?php

  /**
   * Object state interface definition
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IState {
    
    /**
     * Return state helper instance
     *
     * @return IStateImplementation
     */
    function state();
    
  }