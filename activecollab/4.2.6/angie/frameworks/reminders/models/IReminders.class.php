<?php

  /**
   * Reminders interface definition
   * 
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  interface IReminders {
  
  	/**
  	 * Return reminders helper instance
  	 * 
  	 * @return IRemindersImplementation
  	 */
  	function reminders();
  	
  }