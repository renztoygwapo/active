<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Calendars controller
   *
   * @package activeCollab.frameworks.calendars
   * @subpackage controllers
   */
  class FwCalendarsController extends BackendController {

    /**
     * Selected calendar
     *
     * @var Calendar
     */
    protected $active_calendar;

	  /**
	   * State controller delegate
	   *
	   * @var StateController
	   */
	  protected $state_delegate;

	  /**
	   * Construct framework calendars controller
	   *
	   * @param Request $parent
	   * @param mixed $context
	   */
	  function __construct(Request $parent, $context = null) {
		  parent::__construct($parent, $context);

		  if($this->getControllerName() == 'calendars') {
			  $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'calendar');
		  } // if
	  } // __construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(!Calendars::canUse($this->logged_user)) {
        $this->response->notFound();
      } // if

      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('calendars', lang('Calendars'), Router::assemble('calendars'), null, true);

      EventsManager::trigger('on_calendars_tabs', array(&$this->wireframe->tabs, &$this->logged_user));

      $this->wireframe->breadcrumbs->add('calendars', lang('Calendars'), Router::assemble('calendars'));
      $this->wireframe->setCurrentMenuItem('calendars');

      $calendar_id = $this->request->getId('calendar_id');
      if($calendar_id) {
        $this->active_calendar = Calendars::findById($calendar_id);
      } // if

      if($this->active_calendar instanceof UserCalendar) {

        $this->wireframe->breadcrumbs->add('calendar', $this->active_calendar->getName(), $this->active_calendar->getViewUrl());
      } else {
        $this->active_calendar = new UserCalendar();
      } // if

      $this->response->assign('active_calendar', $this->active_calendar);

	    if($this->state_delegate instanceof StateController) {
		    $this->state_delegate->__setProperties(array(
			    'active_object' => &$this->active_calendar,
		    ));
	    } // if

	    $selected_user = $this->logged_user;

	    $selected_user_id = $this->request->getId('user_id');
	    if ($selected_user_id && ($this->logged_user->isAdministrator() || $this->logged_user->isProjectManager())) {
		    $new_selected_user = Users::findById($selected_user_id);
		    if ($new_selected_user instanceof User) {
			    $selected_user = $new_selected_user;
		    } // if
	    } // if

	    $this->response->assign(array(
		    'selected_user' => $selected_user
	    ));
    } // __construct

    /**
     * Show main calendar page
     */
    function index() {
      $this->wireframe->list_mode->enable();

	    // get current date
	    $now = DateValue::now();

	    // get current calendar mode
      $mode = Calendar::DEFAULT_MODE;

	    switch ($mode) {
		    case "weekly":
			    break;
		    case "monthly":
			    $year = $this->request->get('year', $now->getYear());
			    $month = $this->request->get('month', $now->getMonth());

					$first_week_day = ConfigOptions::getValueFor('time_first_week_day', $this->logged_user);
					$last_week_day = $first_week_day - 1 < 0 ? 6 : $first_week_day - 1;
					$first_scale_day = DateValue::beginningOfMonth($month, $year);
			    $last_scale_day = DateValue::endOfMonth($month, $year);

					if ($first_week_day > $first_scale_day->getWeekday()) {
						$first_scale_day->advance((-7 + ($first_week_day - $first_scale_day->getWeekday())) * 86400);
					} elseif ($first_week_day < $first_scale_day->getWeekday()) {
						$first_scale_day->advance((-1 * ($first_scale_day->getWeekday() - $first_week_day)) * 86400);
					} // if

					if ($last_scale_day->getWeekday() > $last_week_day) {
						$last_scale_day->advance((($last_week_day - $last_scale_day->getWeekday()) + 7) * 86400);
					} elseif ($last_scale_day->getWeekday() < $last_week_day) {
						$last_scale_day->advance(($last_week_day - $last_scale_day->getWeekday()) * 86400);
					} // if

					$data_url = Router::assemble('calendars_monthly', array('year' => '--YEAR--', 'month' => '--MONTH--'));
					$current_date = DateValue::makeFromString($year . "-" . $month . "-01");
			    break;
		    case "yearly":
			    break;
		    default:
					$this->response->exception(new Exception('Unknown calendar mode'));
			    break;
	    } // switch

	    // set default user
	    $user = $this->logged_user;

	    // default search options
	    $all_for_admins_and_pms = false;
	    $assigned = true;

	    // set default filter data
	    $filter_data = array(
		    'enabled' => false
	    );

	    // prepare data for filter, access granted for Manager, Member and Subcontractor
	    if ($this->logged_user->isProjectManager() || $this->logged_user->isMember() || $this->logged_user instanceof Subcontractor) {
		    $config = ConfigOptions::getValueFor('default_project_calendar_filter', $this->logged_user);
		    $filter_type = array_var($config, 'type');
		    $filter = $this->request->get('filter', $filter_type);

		    if ($this->logged_user->isProjectManager()) {
			    $filter_options = array(
				    Calendar::FILTER_EVERYTHING_IN_ALL_PROJECTS => lang('Everything in All Projects'),
				    Calendar::FILTER_EVERYTHING_IN_MY_PROJECTS => lang('Everything in My Projects (default)'), // default option
				    Calendar::FILTER_MY_ASSIGNMENTS_IN_MY_PROJECTS => lang('My Assignments in My Projects')
			    );
			    // prepare search options
			    switch ($filter) {
				    case Calendar::FILTER_EVERYTHING_IN_ALL_PROJECTS:
					    $all_for_admins_and_pms = true;
					    $assigned = false;
					    $selected_option = $filter;
					    break;
				    case Calendar::FILTER_MY_ASSIGNMENTS_IN_MY_PROJECTS:
					    $selected_option = $filter;
					    break;
				    case Calendar::FILTER_BY_USER:
					    $user_id = $this->request->getId('id', null, array_var($config, 'value'));
					    if ($user_id) {
						    $user = Users::findById($user_id);
						    if (!($user instanceof User) || $user instanceof Client) {
							    $this->response->notFound();
						    } // if
						    $selected_option = $user->getId();
					    } else {
						    $this->response->notFound();
					    } // if
					    break;
				    default:
					    $assigned = false;
					    $selected_option = $filter = Calendar::FILTER_EVERYTHING_IN_MY_PROJECTS;
					    break;
			    } // switch

			    // except clients from users list
			    $exclude_ids = array($this->logged_user->getId());
			    $all_clients = Users::findByType('Client');
			    if (is_foreachable($all_clients)) {
				    foreach ($all_clients as $client) {
					    if ($client instanceof Client) {
						    $exclude_ids[] = $client->getId();
					    } // if
				    } // foreach
			    } // if

			    // set filter data
			    $filter_data = array(
				    'options' => $filter_options,
				    'users' => Users::getForSelect($this->logged_user, $exclude_ids, STATE_VISIBLE),
				    'selected_option' => $selected_option,
				    'enabled' => true
			    );

		    } elseif ($this->logged_user->isMember() || $this->logged_user instanceof Subcontractor) {
			    $filter_options = array(
				    Calendar::FILTER_EVERYTHING_IN_MY_PROJECTS => lang('Everything in My Projects (default)'), // default option
				    Calendar::FILTER_ONLY_MY_ASSIGNMENTS => lang('Only My Assignments')
			    );
			    // prepare search options
			    switch ($filter) {
				    case Calendar::FILTER_ONLY_MY_ASSIGNMENTS:
					    $selected_option = $filter;
					    break;
				    default:
					    $assigned = false;
					    $selected_option = $filter = Calendar::FILTER_EVERYTHING_IN_MY_PROJECTS;
					    break;
			    } // switch

			    // set filter data
			    $filter_data = array(
				    'options' => $filter_options,
				    'selected_option' => $selected_option,
				    'enabled' => true
			    );
		    } else {
			    $this->response->forbidden();
		    } // if

		    // set new default project calendar filter config
		    $new_config_options = array(
			    'type' => $filter
		    );
		    if ($user->getId() != $this->logged_user->getId()) {
			    $new_config_options['value'] = $user->getId();
		    } // if

		    ConfigOptions::setValueFor('default_project_calendar_filter', $this->logged_user, $new_config_options);
	    } // if

	    // include completed and archived
	    $include_completed_and_archived = true;

	    // find all calendar events for given user
	    $events = CalendarEvents::findForList($this->logged_user, $first_scale_day, $last_scale_day);
	    EventsManager::trigger('on_calendar_events', array(&$events, $first_scale_day, $last_scale_day, $user, $assigned, $all_for_admins_and_pms, $include_completed_and_archived));

	    if ($this->request->isPageCall()) {
	      $calendar_groups = array(
		      'default' => array(
			      'label'     => lang('Calendars'),
			      'calendars' => Calendars::findForList($this->logged_user),
			      'options'   => Calendars::getDefaultGroupOptions($this->logged_user)
		      ),
	      );

	      EventsManager::trigger('on_calendar_groups', array(&$user, &$calendar_groups, $all_for_admins_and_pms));

		    // prepare days off pool
	      $days_off = DayOffs::find();
	      $days_off_pool = array();
	      if($days_off) {
		      foreach($days_off as $day_off) {
			      if ($day_off instanceof DayOff) {
				      $days_off_pool[] = array(
					      'name' => $day_off->getName(),
					      'date' => $day_off->getEventDate(),
					      'repeat_yearly' => $day_off->getRepeatYearly(),
				      );
			      } // if
		      } // foreach
	      } // if

		    $calendar_config = array(
			    'calendar_data' => array(
				    'is_feed_user'      => $user->isFeedUser(),
				    'groups'    => $calendar_groups,
				    'urls'      => array(
					    'data'                    => $data_url,
					    'calendars_add'           => Router::assemble('calendars_add'),
					    'events_add'              => Router::assemble('events_add'),
					    'mass_visibility'         => Router::assemble('calendar_mass_change_visibility'),
					    'sidebar_toggle'          => Router::assemble('calendars_sidebar_toggle')
				    ),
				    'current_date' => $current_date,
				    'mode'      => $mode,
				    'events'    => $events,
				    'settings'  => array(
					    'first_week_day'          => $first_week_day,
					    'last_week_day'           => $last_week_day,
					    'default_calendar_color'  => Calendar::DEFAULT_COLOR,
					    'logged_user'             => $this->logged_user,
					    'days_off'                => $days_off_pool,
					    'sidebar_hidden'          => ConfigOptions::getValueFor('calendar_sidebar_hidden', $this->logged_user),
					    'can_add_event'           => CalendarEvents::canAddGlobal($this->logged_user)
				    ),
				    'filter' => $filter_data
			    )
		    );

		    // get default work days
		    $work_days = ConfigOptions::getValueFor('time_workdays', $this->logged_user);
		    if ($work_days) {
			    $calendar_config['calendar_data']['settings']['work_days'] = $work_days;
		    } // if

        // calendar data
        $this->response->assign($calendar_config);
      } else {
	      $this->response->respondWithData($events);
      } // if
    } // index

	  /**
	   * Serve iCal data
	   */
	  function ical() {
		  if ($this->active_calendar->isLoaded()) {
			  if ($this->logged_user->isFeedUser()) {

				  $objects = CalendarEvents::findByCalendar($this->active_calendar);

				  render_calendar_icalendar($this->active_calendar->getName(), $objects);
				  die();
			  } else {
				  $this->response->forbidden();
			  } //if
		  } else {
			  $this->response->notFound();
		  } // if
	  } // ical

	  /**
	   * Show iCal subscribe page
	   */
	  function ical_subscribe() {
		  if ($this->active_calendar->isLoaded()) {
			  if ($this->logged_user->isFeedUser()) {
				  $this->wireframe->hidePrintButton();
				  $feed_token  = $this->logged_user->getFeedToken();

				  $ical_url = Router::assemble('calendar_ical', array('calendar_id' => $this->active_calendar->getId(), 'auth_api_token' => $feed_token));

				  $ical_subscribe_url = str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $ical_url);

				  $this->response->assign(array(
					  'ical_url' => $ical_url,
					  'ical_subscribe_url' => $ical_subscribe_url
				  ));
			  } else {
				  $this->response->forbidden();
			  } //if
		  } else {
			  $this->response->notFound();
		  } // if
	  } // ical_subscribe

    /**
     * Import external calendar
     */
    function import() {
      if($this->request->isAsyncCall()) {
        if(Calendars::canAdd($this->logged_user)) {
	        // @todo srediti import
	        $this->response->exception(new Error("Import option is not implemented yet"));
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // import

	  function import_feed() {
		  if($this->request->isAsyncCall()) {
			  if (Calendars::canAdd($this->logged_user)) {
				  $feed_data = $this->request->post('feed');

				  $this->response->assign('feed_data', $feed_data);

				  if ($this->request->isSubmitted()) {
					  try {
						  DB::beginWork("Create calendar from feed @ ". __CLASS__);

						  $config = array(
							  'unique_id' => make_string(10) . '-' . make_string(10)
						  );
						  $v = new vcalendar($config);

						  // iCalcreator also support remote files
						  $v->setConfig('url', array_var($feed_data, 'url'));
						  $v->parse();

//						  $calendar_data = array(
//							  'name' => $v->getProperty('X-WR-CALNAME'),
//							  'color' => Calendar::DEFAULT_COLOR,
//							  'share_type' => Calendar::DONT_SHARE
//						  );
//
//						  $this->active_calendar = new ExternalCalendar();
//							$this->active_calendar->setAttributes($calendar_data);
//						  $this->active_calendar->setState(STATE_VISIBLE);
//						  $this->active_calendar->setPosition(Calendars::getNextPosition());
//						  $this->active_calendar->save();

						  $result = array();
						  $calendar_events_pool = array();
						  while( $vevent = $v->getComponent('vevent')) {
							  $repeat = $vevent->getProperty('rrule');
							  $result[] = $repeat;
//							  $calendar_event_data = array(
//								  'name' => $vevent->getProperty('summary'),
//								  'starts_on' => $vevent->getProperty('dtstart'),
//								  'ends_on' => $vevent->getProperty('dtend'),
//							  );
//
//							  $calendar_event = $this->active_calendar->calendarEvents()->newEvent();
//							  $calendar_event->set
//							  $calendar_events_pool[] = $calendar_event->describe($this->logged_user, true);
						  }

						  var_dump($result); exit;

						  DB::commit("Calendar created from feed @ ". __CLASS__);

						  $this->response->assign(array(
							  'calendar' => array()
						  ));

						  //$this->response->exception(new Error("Import feed option is not implemented yet"));
					  } catch (Exception $e) {
						  DB::rollback("Failed to create calendar from feed @ ". __CLASS__);

						  $this->response->exception($e);
					  } // try
				  } // if
			  } else {
				  $this->response->forbidden();
			  } // if
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // import_feed

	  function import_file() {
		  if($this->request->isAsyncCall()) {
			  if (Calendars::canAdd($this->logged_user)) {

				  if ($this->request->isSubmitted()) {
					  try {
						  $uploaded_file = array_var($_FILES, 'file', null);

						  //var_dump($uploaded_file); exit;

						  if ($uploaded_file['error']) {
							  throw new Error(get_upload_error_message($uploaded_file['error']));
						  } // if

						  if (!$uploaded_file) {
							  throw new Error(lang('File not uploaded correctly'));
						  } // if

						  if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($uploaded_file['size'])) {
							  throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
						  } // if

						  $new_name = AngieApplication::getAvailableWorkFileName("ical", 'ics');
						  if (!move_uploaded_file($uploaded_file['tmp_name'], $new_name)) {
							  throw new Error(lang('Could not move uploaded file to uploads folder. Check folder permissions'));
						  } // if

						  //require_once ANGIE_PATH . '/classes/icalendar/iCalcreator.class.php';

						  $config = array(
							  'unique_id' => make_string(10) . '-' . make_string(10)
						  );
						  $v = new vcalendar($config);

						  $config = array(
							  'directory' => WORK_PATH,
							  'filename' => str_replace(WORK_PATH . "/", "", $new_name)
						  );
						  $v->setConfig($config);
						  $v->parse();

						  // we don't need it anymore...
						  @unlink($new_name);

						  $result = array();

						  while( $vevent = $v->getComponent('vevent')) {
							  $result[] = $vevent->getProperty( "summary" );
						  }
						  var_dump($result); exit;

						  // @todo implement import file option
						  //$this->response->exception(new Error("Import file option is not implemented yet"));
					  } catch (Exception $e) {
						  if ($new_name && is_file($new_name)) {
							  @unlink($new_name);
						  } // if

						  $this->response->respondWithData($e);
					  } // try
				  } // if
			  } else {
				  $this->response->forbidden();
			  } // if
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // import_file

    /**
     * Show calendar details (API only)
     */
    function view() {
      if($this->active_calendar->isAccessible()) {
        if($this->active_calendar->canView($this->logged_user)) {
          $this->response->respondWithData($this->active_calendar, array(
            'as' => 'calendar',
            'detailed' => true, 
          ));
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view

    /**
     * Define a new calendar
     */
    function add() {
      if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Calendars::canAdd($this->logged_user)) {
          $calendar_data = $this->request->post('calendar', array(
	          'share_type' => Calendar::DONT_SHARE
          ));

          $this->response->assign('calendar_data', $calendar_data);

          if($this->request->isSubmitted()) {
            try {
	            DB::beginWork("Create calendar @ ". __CLASS__);
              $this->active_calendar->setAttributes($calendar_data);
              $this->active_calendar->setState(STATE_VISIBLE);
              $this->active_calendar->setPosition(Calendars::getNextPosition());
              $this->active_calendar->save();

	            $user_ids = array_var($calendar_data, 'user_ids');
	            if (is_foreachable($user_ids)) {
		            foreach ($user_ids as $user_id) {
			            $user = Users::findById($user_id);
			            if ($user instanceof User) {
				            $this->active_calendar->users()->add($user);
			            } // if
		            } // foreach
	            } // if

	            $this->active_calendar->setColor(array_var($calendar_data, 'color'));

	            DB::commit("Calendar created @ ". __CLASS__);
              $this->response->respondWithData($this->active_calendar, array(
                'detailed' => true,
                'as' => 'calendar',
              ));
            } catch(Exception $e) {
	            DB::rollback("Failed to create calendar @ ". __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Update calendar details
     */
    function edit() {
	    if($this->active_calendar->isAccessible()) {
		    if($this->active_calendar->canEdit($this->logged_user)) {
			    $calendar_data = $this->request->post('calendar', array(
				    'name'                    => $this->active_calendar->getName(),
				    'color'                   => $this->active_calendar->getColor(),
				    'user_ids'                => $this->active_calendar->users()->getIds(),
				    'share_can_add_events'    => $this->active_calendar->getShareCanAddEvents(),
				    'share_type'              => $this->active_calendar->getShareType()
			    ));

			    $this->response->assign('calendar_data', $calendar_data);

			    if($this->request->isSubmitted()) {
				    try {
					    DB::beginWork('Updating calendar @ ' . __CLASS__);
					    $this->active_calendar->setAttributes($calendar_data);
					    $this->active_calendar->save();

					    $user_ids = array_var($calendar_data, 'user_ids');
					    $this->active_calendar->users()->clear($this->logged_user);
					    if (is_foreachable($user_ids)) {
						    foreach ($user_ids as $user_id) {
							    $user = Users::findById($user_id);
							    if ($user instanceof User) {
								    $this->active_calendar->users()->add($user);
							    } // if
						    } // foreach
					    } // if

					    $this->active_calendar->setColor(array_var($calendar_data, 'color'));

					    DB::commit('Calendar updated @ ' . __CLASS__);
					    $this->response->respondWithData($this->active_calendar, array(
						    'detailed' => true,
						    'as' => 'calendar',
					    ));
				    } catch(Exception $e) {
					    DB::rollback('Failed to update calendar @ ' . __CLASS__);
					    $this->response->exception($e);
				    } // try
			    } // if

			    $this->active_calendar->accessLog()->log($this->logged_user);
		    } else {
			    $this->response->forbidden();
		    } // if
	    } else {
		    $this->response->notFound();
	    } // if
    } // edit

    /**
     * Drop selected calendar
     */
    function delete() {
	    if($this->active_calendar->isAccessible()) {
		    if($this->active_calendar->canDelete($this->logged_user)) {
			    try {
				    $this->active_calendar->delete();

				    $this->response->respondWithData($this->active_calendar, array(
					    'as' => 'calendar',
					    'detailed' => true,
				    ));
			    } catch(Exception $e) {
				    $this->response->exception($e);
			    } // try
		    } else {
			    $this->response->forbidden();
		    } // if
		  } else {
		    $this->response->badRequest();
	    } // if
    } // delete

	  /**
	   * Change Color
	   */
	  function change_color() {
		  if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			  $type = $this->request->get('type', 'calendar');
			  $id = $this->request->getId('type_id', null);
			  if ($id) {
				  $this->response->assign('calendar_change_color_url', Router::assemble('calendar_change_color_by_type', array('type' => $type, 'type_id' => $id)));
			  } else {
				  $id = $this->request->getId('calendar_id');
				  $this->response->assign('calendar_change_color_url', Router::assemble('calendar_change_color', array('calendar_id' => $id)));
			  } // if


			  $type = Inflector::camelize($type);
			  $object = DataObjectPool::get($type, $id);

			  if ($object) {
				  if ($object instanceof Calendar) {
					  $type = $object->getType();
					  $color = $object->getColor();
				  } elseif ($object instanceof ICalendarContext) {
					  $color = $object->calendar_context()->getColor();
				  }// if

				  $calendar_data =  $this->request->post('calendar', array(
					  'color' => $color
				  ));

				  $this->response->assign('calendar_data', $calendar_data);

					if ($this->request->isSubmitted()) {
						if ($object instanceof Calendar) {
							$object->setColor(array_var($calendar_data, 'color'));
							$this->response->respondWithData($object, array(
								'detailed' => true,
								'as' => 'calendar',
							));
						} else {
							$object->calendar_context()->setColor(array_var($calendar_data, 'color'));
							$this->response->respondWithData($object->calendar_context()->describe($this->logged_user));
						} // if
					} // if
			  } else {
				  $this->response->notFound();
			  } // if
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // change_color

	  /**
	   * Change Visibility
	   */
	  function change_visibility() {
		  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			  $type = $this->request->get('type', 'calendar');
			  $id = $this->request->getId('type_id', null);
			  if (!$id) {
				  $id = $this->request->getId('calendar_id');
			  } // if

			  $type = Inflector::camelize($type);
			  $object = DataObjectPool::get($type, $id);

			  if ($object instanceof Calendar) {
				  $type = $object->getType();
				  $visible = $object->isVisible();
			  } elseif ($object instanceof ICalendarContext) {
				  $visible = $object->calendar_context()->isVisible();
			  } // if

			  $visible = $this->request->post('visible', $visible);

			  if ($object instanceof Calendar) {
				  $object->setVisible($visible);
			  } elseif ($object instanceof ICalendarContext) {
				  $object->calendar_context()->setVisible($visible);
			  } // if

			  $this->response->ok();
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // change_visibility

    /**
     * Mass change visibility
     */
    function mass_change_visibility () {
      if (!($this->request->isAsyncCall() && $this->request->isSubmitted())) {
        $this->response->badRequest();
      } // if

      $calendars = $this->request->post('calendars', array());
      $visible = $this->request->post('visible', null);
      if (!is_foreachable($calendars) || $visible === null) {
        $this->response->operationFailed();
      } // if

      foreach ($calendars as $calendar) {
        $object = DataObjectPool::get(Inflector::camelize($calendar['type']), $calendar['id']);
        if ($object instanceof Calendar) {
          $object->setVisible($visible);
        } elseif ($object instanceof ICalendarContext) {
          $object->calendar_context()->setVisible($visible);
        } // if
      } // foreach

      $this->response->ok();
    } // mass_change_visibility

    /**
     * Toggle sidebar visibility
     */
    function sidebar () {
      if (!($this->request->isAsyncCall() && $this->request->isSubmitted())) {
        $this->response->badRequest();
      } // if

      $visible = $this->request->post('visible', null);
      if ($visible === null) {
        $this->response->operationFailed();
      } // if

      ConfigOptions::setValueFor('calendar_sidebar_hidden', $this->logged_user, (int) $visible);

      $this->response->ok();
    } // sidebar

	  /**
	   * Add event to preselected calendar
	   */
	  function add_event() {
		  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			  if (CalendarEvents::canAddGlobal($this->logged_user) && !$this->request->isSubmitted()) {
				  $calendar_event_data = array(
					  'starts_on' => DateValue::makeFromString($this->request->get('date', DateValue::now())),
					  'ends_on' => DateValue::makeFromString($this->request->get('date', DateValue::now())),
					  'parent_id' => $this->request->get('parent_id', 0),
					  'repeat_event' => CalendarEvent::DONT_REPEAT,
					  'repeat_event_option' => CalendarEvent::REPEAT_OPTION_FOREVER,
					  'repeat_until' => DateValue::now(),
					  'starts_on_time' => null
				  );

				  $this->response->assign('calendar_event_data', $calendar_event_data);
				  $this->setView(get_view_path('add', 'fw_calendar_events', CALENDARS_FRAMEWORK));
			  } else {
				  $this->response->forbidden();
			  } // if
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // add_event

  }