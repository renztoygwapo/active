<?php

  /**
   * User reminders helper instance
   * 
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  class IUserRemindersImplementation {
  
    /**
     * Parent object (user)
     *
     * @var User
     */
    protected $object;
    
    /**
     * Construct user reminders helper instance
     * 
     * @param User $object
     */
    function __construct(User $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Return user reminders URL
     * 
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_reminders', $this->object->getRoutingContextParams());
    } // getUrl
    
  }