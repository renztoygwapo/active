<?php

  /**
   * Intereface for all schedulable objects
   *
   * @package angie.frameworks.schedule
   * @subpackage models
   */
  interface ISchedule {
    
    /**
     * Return schedule helper for this object
     *
     * @return IScheduleImplementation
     */
    public function schedule();
    
    /**
     * Get due on date
     * 
     * @return DateValue
     */
    public function getDueOn();
    
    /**
     * Set due on date
     * 
     * @param DateValue $date
     */
    public function setDueOn($date);
    
  }