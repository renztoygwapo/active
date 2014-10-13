<?php

  /**
   * Requirements for commentable objects
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  interface IComments {
    
    /**
     * Return comments interface instance
     *
     * @return ICommentsImplementation
     */
    function &comments();
    
  }