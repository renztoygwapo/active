<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_calendar_events', CALENDARS_FRAMEWORK);

  /**
   * Calendar events controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CalendarEventsController extends FwCalendarEventsController {

	  /**
	   * Construct calendar events controller
	   *
	   * @param Request $parent
	   * @param mixed $context
	   */
	  function __construct(Request $parent, $context = null) {
		  parent::__construct($parent, $context);

		  if($this->getControllerName() == 'calendar_events') {
			  if(AngieApplication::isModuleLoaded('footprints')) {
				  $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'calendar_event');
				  $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'calendar_event');
			  } // if
		  } // if
	  } // __construct

	  /**
	   * Execute before any action
	   */
	  function __before() {
		  parent::__before();

		  if ($this->access_logs_delegate instanceof AccessLogsController) {
			  $this->access_logs_delegate->__setProperties(array(
				  'active_object' => &$this->active_calendar_event
			  ));
		  } // if

		  if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
			  $this->history_of_changes_delegate->__setProperties(array(
				  'active_object' => &$this->active_calendar_event
			  ));
		  } // if
	  } // __before

  }