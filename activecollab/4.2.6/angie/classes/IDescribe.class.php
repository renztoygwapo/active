<?php

  /**
   * Describe interface
   * 
   * @package angie.frameworks.environment
   */
  interface IDescribe {
    
    /**
     * Describe object
     * 
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     */
    function describe(IUser $user, $detailed = false, $for_interface = false);
    
  }