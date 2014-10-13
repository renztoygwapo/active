<?php

  /**
   * Interface that all objects that have subscribers need to implement
   *
   * @package angie.frameworks.subscriptions
   * @subpackage models
   */
  interface ISubscriptions {
    
    /**
     * Return object's subscriptions helper
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions();
    
  }