<?php

  // Build on top of calendars framework
  AngieApplication::useController('calendars', CALENDARS_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level calendar events controller
   *
   * @package angie.frameworks.calendars
   * @subpackage controllers
   */
  abstract class FwCalendarEventsController extends CalendarsController {

    /**
     * Selected and loaded calendar event
     *
     * @var CalendarEvent
     */
    protected $active_calendar_event;

	  /**
	   * State controller delegate
	   *
	   * @var StateController
	   */
	  protected $state_delegate;

	  /**
	   * Construct framework calendar events controller
	   *
	   * @param Request $parent
	   * @param mixed $context
	   */
	  function __construct(Request $parent, $context = null) {
		  parent::__construct($parent, $context);

		  if($this->getControllerName() == 'calendar_events') {
			  $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'calendar_event');
		  } // if
	  } // __construct

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if($this->active_calendar->isNew()) {
        $this->response->notFound();
      } // if

      $calendar_event_id = $this->request->getId('calendar_event_id');
	    $this->active_calendar_event = CalendarEvents::findById($calendar_event_id);

      if($this->active_calendar_event instanceof CalendarEvent) {
        if(!($this->active_calendar_event->getParent() instanceof Calendar) || !$this->active_calendar_event->getParent()->is($this->active_calendar)) {
          $this->response->notFound();
        } // if

        $this->wireframe->breadcrumbs->add('calendar_event', $this->active_calendar_event->getName(), $this->active_calendar_event->getViewUrl());
      } else {
	      $this->active_calendar_event = $this->active_calendar->calendarEvents()->newEvent();
      } // if

	    $this->response->assign(array(
		    'active_calendar_event' => $this->active_calendar_event
	    ));

	    if($this->state_delegate instanceof StateController) {
		    $this->state_delegate->__setProperties(array(
			    'active_object' => &$this->active_calendar_event,
		    ));
	    } // if
    } // __before

    /**
     * Show details of a specific event
     */
    function view() {
      if($this->active_calendar_event->isAccessible()) {
				$this->response->redirectTo('calendars');
      } else {
        $this->response->notFound();
      } // if
    } // view

    /**
     * Create a new event
     */
    function add() {
	    if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
				if (CalendarEvents::canAdd($this->logged_user, $this->active_calendar)) {
					$calendar_event_data = $this->request->post('calendar_event', array(
						'starts_on' => $this->request->get('starts_on'),
						'ends_on' => $this->request->get('ends_on'),
						'repeat_event' => CalendarEvent::DONT_REPEAT,
						'repeat_event_option' => CalendarEvent::REPEAT_OPTION_FOREVER,
						'repeat_until' => DateValue::now(),
						'starts_on_time' => null
					));

					$this->response->assign('calendar_event_data', $calendar_event_data);
					$calendar_event_data = $this->request->post('calendar_event');

					$this->response->assign('calendar_event_data', $calendar_event_data);

					if ($this->request->isSubmitted()) {
						// find calendar that user can manage by id
						try {
							DB::beginWork('Creating new calendar event @ ' . __CLASS__);

							$starts_on_time = array_var($calendar_event_data, 'starts_on_time', null, true);
							$time_string = array_var($starts_on_time, 'value', null);
							$time_meridiem = array_var($starts_on_time, 'meridiem', null);
							$all_day_event = array_var($starts_on_time, 'all_day_event', false);
							$repeat_event = array_var($calendar_event_data, 'repeat_event');
							if ($repeat_event == CalendarEvent::DONT_REPEAT) {
								$calendar_event_data['repeat_event_option'] = null;
							} else {
								$repeat_event_option = array_var($calendar_event_data, 'repeat_event_option');
							} // if
							$repeat_until_value = array_var($calendar_event_data, 'repeat_until_value', array(), true);

							$this->active_calendar_event->setAttributes($calendar_event_data);
							if (!$all_day_event) {
								$date_time = $this->active_calendar_event->getStartsOn()->toMySQL() . " " . $time_string . ($time_meridiem ? $time_meridiem : '');
								$date_time = DateTimeValue::makeFromString($date_time)->toMySQL();
								$this->active_calendar_event->setStartsOnTime($date_time);
							} // if
							$this->active_calendar_event->setRepeatUntil($repeat_event, $repeat_event_option, $repeat_until_value);
							$this->active_calendar_event->setParent($this->active_calendar);
							$this->active_calendar_event->save();

							DB::commit('New calendar event created @ ' . __CLASS__);

							$this->response->respondWithData($this->active_calendar_event, array(
								'detailed' => true,
								'as' => 'calendar_event',
							));
						} catch(Exception $e) {
							DB::rollback('Failed to create new calendar event @ ' . __CLASS__);
							$this->response->exception($e);
						} // try
					} // if
				} else {
					$this->response->forbidden();
				} // if
	    } else {
		    $this->response->badRequest();
	    }
    } // add

    /**
     * Update an existing event
     */
    function edit() {
      if($this->active_calendar_event->isAccessible()) {
        if($this->active_calendar_event->canEdit($this->logged_user)) {
	        $calendar_event_data = $this->request->post('calendar_event', array(
		        'name' => $this->active_calendar_event->getName(),
		        'parent_id' => $this->active_calendar_event->getParentId(),
		        'starts_on' => $this->active_calendar_event->getStartsOn(),
		        'ends_on' => $this->active_calendar_event->getEndsOn(),
		        'repeat_event' => $this->active_calendar_event->getRepeatEvent(),
		        'repeat_event_option' => $this->active_calendar_event->getRepeatEventOption() ? $this->active_calendar_event->getRepeatEventOption() : CalendarEvent::REPEAT_OPTION_FOREVER,
		        'repeat_until' => $this->active_calendar_event->getRepeatUntil(),
		        'starts_on_time' => $this->active_calendar_event->getStartsOnTime() ? DateTimeValue::makeFromString($this->active_calendar_event->getStartsOnTime()) : null,
	        ));

	        $this->response->assign('calendar_event_data', $calendar_event_data);

					if ($this->request->isSubmitted()) {
						$parent_id = array_var($calendar_event_data, 'parent_id');
						$calendar = null;
						if ($parent_id && $parent_id != $this->active_calendar_event->getId()) {
							$calendar = Calendars::findById($parent_id);
							if (!($calendar instanceof Calendar && $calendar->isAccessible() && CalendarEvents::canAdd($this->logged_user, $calendar))) {
								$this->response->forbidden();
							} // if
						} // if

						try {
							DB::beginWork('Updating calendar event @ ' . __CLASS__);

							$starts_on_time = array_var($calendar_event_data, 'starts_on_time', null, true);
							$time_string = array_var($starts_on_time, 'value', null);
							$time_meridiem = array_var($starts_on_time, 'meridiem', null);
							$all_day_event = array_var($starts_on_time, 'all_day_event', false);
							$repeat_event = array_var($calendar_event_data, 'repeat_event');
							if ($repeat_event == CalendarEvent::DONT_REPEAT) {
								$calendar_event_data['repeat_event_option'] = null;
							} else {
								$repeat_event_option = array_var($calendar_event_data, 'repeat_event_option');
							} // if
							$repeat_until_value = array_var($calendar_event_data, 'repeat_until_value', array(), true);


							$this->active_calendar_event->setAttributes($calendar_event_data);
							if ($starts_on_time) {
								if (!$all_day_event) {
									$date_time = $this->active_calendar_event->getStartsOn()->toMySQL() . " " . $time_string . ($time_meridiem ? $time_meridiem : '');
									$date_time = DateTimeValue::makeFromString($date_time)->toMySQL();
									$this->active_calendar_event->setStartsOnTime($date_time);
								} else {
									$this->active_calendar_event->setStartsOnTime(null);
								} // if
							} // if
							$this->active_calendar_event->setRepeatUntil($repeat_event, $repeat_event_option, $repeat_until_value);
							if ($calendar instanceof Calendar) {
								$this->active_calendar_event->setParent($calendar);
							} // if
							$this->active_calendar_event->save();

							DB::commit('Calendar event updated @ ' . __CLASS__);

							if ($this->request->isPageCall()) {

							} else {
								$this->response->respondWithData($this->active_calendar_event, array(
									'detailed' => true,
									'as' => 'calendar_event',
								));
							} // if
						} catch (Exception $e) {
							DB::rollback('Failed to update calendar event @ ' . __CLASS__);
							$this->response->exception($e);
						} // try
					} // if

	        $this->active_calendar_event->accessLog()->log($this->logged_user);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // edit

    /**
     * Delete existing template
     */
    function delete() {
      if($this->active_calendar_event->isAccessible()) {
        if($this->active_calendar_event->canDelete($this->logged_user)) {
	        try {
		        $this->active_calendar_event->delete();

		        $this->response->respondWithData($this->active_calendar_event, array(
			        'as' => 'calendar_event',
			        'detailed' => true,
		        ));
	        } catch(Exception $e) {
		        $this->response->exception($e);
	        } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // delete

  }