<?php

  /**
   * Interface that implement all objects that are subrouting context
   *
   * @package angie.library.routing
   */
  interface IRoutingContext {
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext();
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams();
    
  }