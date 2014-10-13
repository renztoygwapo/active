<?php

  /**
   * Home screen interface
   * 
   * @package angie.framework.homescreens
   * @subpackage models
   */
  interface IHomescreen {
  
    /**
     * Return homescreen helper instance
     * 
     * @return IHomescreenImplementation
     */
    function homescreen();
    
  }