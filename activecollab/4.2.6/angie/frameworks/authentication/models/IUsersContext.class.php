<?php

  /**
   * Users context interface
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  interface IUsersContext {
    
    /**
     * Return users helper instance
     *
     * @return IUsersImplementation
     */
    function users();
    
  }