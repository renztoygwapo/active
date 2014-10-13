<?php

  /**
   * Subtask class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class Subtask extends FwSubtask implements ISchedule, ICalendarEventContext {
    
    /**
     * Schedule helper
     * 
     * @var IScheduleImplementation
     */
    private $schedule = false;
    
    /**
     * Return schedule helper instance
     * 
     * @return IScheduleImplementation
     */
    function schedule() {
    	if ($this->schedule === false) {
    		$this->schedule = new IScheduleImplementation($this);	
    	} // if
    	
    	return $this->schedule;
    } // schedule

	  /**
	   * Cached calendar event context helper instance
	   *
	   * @var ISubtaskCalendarEventContextImplementation
	   */
	  private $calendar_event_context = false;

	  /**
	   * Return calendar event context helper instance
	   *
	   * @return ISubtaskCalendarEventContextImplementation
	   */
	  function calendar_event_context() {
		  if($this->calendar_event_context === false) {
			  $this->calendar_event_context = new ISubtaskCalendarEventContextImplementation($this);
		  } // if

		  return $this->calendar_event_context;
	  } // calendar_event_context
    
  }