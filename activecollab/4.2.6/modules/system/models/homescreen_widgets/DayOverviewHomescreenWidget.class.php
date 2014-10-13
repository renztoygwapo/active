<?php

  /**
   * Day overview widget
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class DayOverviewHomescreenWidget extends HomescreenWidget {

    /**
     * Options for day filter
     *
     * @var array
     */
    private $filter_option_day = array();

    /**
     * Options for user filter
     *
     * @var array
     */
    private $filter_option_user = array();

    /**
     * Options for project filter
     *
     * @var array
     */
    private $filter_option_project = array();

    /**
     * Due date calculated from widget options
     *
     * @var DateValue
     */
    var $due_date = null;

    /**
     * User
     *
     * @var IUser
     */
    var $selected_user = null;

    /**
     * Projects
     *
     * @var array
     */
    var $projects = null;

    /**
     * Day options
     */
    const FILTER_DAY_LAST_BUSSINESS_DAY = 'last_business_day';
    const FILTER_DAY_YESTERDAY = 'yesterday';
    const FILTER_DAY_TODAY = 'today';
    const FILTER_DAY_TOMORROW = 'tomorrow';
    const FILTER_DAY_NEXT_BUSINESS_DAY = 'next_business_day';
    const FILTER_DAY_SELECTED_DATE = 'selected_date';

    /**
     * User options
     */
    const FILTER_USER_LOGGED = 'logged_user';
    const FILTER_USER_SELECTED = 'selected_user';

    /**
     * Project options
     */
    const FILTER_PROJECTS_ALL = 'all_active';
    const FILTER_PROJECTS_SELECTED = 'selected_projects';

    /**
     * Set predefined options for each filter
     *
     * @param integer $id
     */
    function __construct($id = null) {
      parent::__construct($id);

      $this->filter_option_day = array(
        self::FILTER_DAY_LAST_BUSSINESS_DAY => lang("Last Business Day"),
        self::FILTER_DAY_YESTERDAY          => lang("Yesterday"),
        self::FILTER_DAY_TODAY              => lang("Today"),
        self::FILTER_DAY_TOMORROW           => lang("Tomorrow"),
        self::FILTER_DAY_NEXT_BUSINESS_DAY  => lang("Next Business Day"),
        self::FILTER_DAY_SELECTED_DATE      => lang("Selected Date ...")
      );

      $this->filter_option_user = array(
        self::FILTER_USER_LOGGED    => lang('Logged User'),
        self::FILTER_USER_SELECTED  => lang('Selected User ...')
      );

      $this->filter_option_project = array(
        self::FILTER_PROJECTS_ALL       => lang('All Active'),
        self::FILTER_PROJECTS_SELECTED  => lang('Selected Projects ...')
      );
    } // __construct

    /**
     * Return widget name
     *
     * @return string
     */
    function getName() {
      return lang('Day Overview');
    } // getName

    /**
     * Return widget description
     *
     * @return string
     */
    function getDescription() {
      return lang('View time, expenses and assignment reports for a specific day');
    } // getDescription

    /**
     * Return widget title
     *
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderTitle(IUser $user, $widget_id, $column_wrapper_class = null) {
      AngieApplication::useHelper('user_link', AUTHENTICATION_FRAMEWORK);
      $this->setWidgetFilters($user);
      return clean($this->getName()) . ' (' . clean($this->due_date->formatForUser($user)) . ' - ' . smarty_function_user_link(array('user' => $this->selected_user), SmartyForAngie::getInstance()) . ')';
    } // renderTitle

    /**
     * Return widget body
     *
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     * @throws Exception
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $this->setWidgetFilters($user);
      $template = SmartyForAngie::getInstance()->createTemplate(get_view_path('/homescreen_widgets/day_overview', null, SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));

      $tomorrow = new DateValue(time()+86400+get_user_gmt_offset($user));
      $is_overview_in_future = $this->due_date->beginningOfDay()->getTimestamp() >= $tomorrow->beginningOfDay()->getTimestamp();
      $is_overview_today = $this->due_date->isToday(get_user_gmt_offset($user));
      $is_overview_in_past = (!$is_overview_today && !$is_overview_in_future);

      $due_on_operator = $is_overview_in_future ? "=" : "<=";

      $total_time = 0;
      $total_expenses = array();
      $due_date = array();

      $completable_object_types = get_completable_project_object_types();

      if (is_foreachable($this->projects)) {
        $project_ids = array_keys($this->projects);

        // prepare array of projects
        foreach ($this->projects as $key => $project) {
          $due_date[$key]['project'] = $project;
          $due_date[$key]['objects_active'] = array();
          $due_date[$key]['objects_completed'] = array();
        } // foreach

        /**
         * Look for project objects in those projects
         */
        $project_objects = ProjectObjects::find(array(
          'conditions' => array("project_id IN (?) AND type IN (?) AND due_on $due_on_operator ? AND (delegated_by_id = ? OR assignee_id = ?) AND visibility >= ? AND state = ?",
            $project_ids,
            $completable_object_types,
            $this->due_date->toMySQL(),
            $this->selected_user->getId(), $this->selected_user->getId(),
            $this->selected_user->getMinVisibility(),
            STATE_VISIBLE
          )
      	));

        if (is_foreachable($project_objects)) {
      	  $project_objects_ids = array();
      	  foreach ($project_objects as $project_object) {
      	    $project_objects_ids[] = $project_object->getId();

      	  	if (is_null($project_object->getCompletedOn())) {
      	  	  $due_date[$project_object->getProjectId()]['objects_active'][] = $project_object;
      	  	} else {
      	  	  if ($project_object->getDueOn()->toMySQL() == $this->due_date->toMySQL()  && !$is_overview_in_future) {
      	  	    $due_date[$project_object->getProjectId()]['objects_completed'][] = $project_object;
      	  	  } // if
      	  	} // if
      	  } // foreach
      	} // if

        /**
      	 * Look for any subtasks added to objects in this project
      	 */
      	$parent_ids = DB::executeFirstColumn("SELECT id FROM ".TABLE_PREFIX."project_objects WHERE project_id IN (?) AND type IN (?) AND state = ?", $project_ids, $completable_object_types, STATE_VISIBLE);
      	if (is_foreachable($parent_ids)) {
      	  $subtasks = Subtasks::find(array(
      		  'conditions' => array("parent_id IN (?) AND due_on $due_on_operator ? AND (delegated_by_id = ? OR assignee_id = ?) AND state = ?",
              $parent_ids,
              $this->due_date->toMySQL(),
              $this->selected_user->getId(), $this->selected_user->getId(),
              STATE_VISIBLE
            )
          ));

      		if (is_foreachable($subtasks)) {
      		  foreach ($subtasks as $subtask) {
      		  	if (is_null($subtask->getCompletedOn())) {
      		  	  $due_date[$subtask->getProjectId()]['objects_active'][] = $subtask;
      		  	} else {
      		  	  if ($subtask->getDueOn()->toMySQL() == $this->due_date->toMySQL() && !$is_overview_in_future) {
      		  	   $due_date[$subtask->getProjectId()]['objects_completed'][] = $subtask;
      		  	  }
      		  	} // if
      		  } // foreach
      		} // if
      	} // if

        // Remove project if there's nothing to display from it
        foreach($due_date as $key => $project) {
          if (!is_foreachable($due_date[$key]['objects_active']) && !is_foreachable($due_date[$key]['objects_completed'])) {
            unset($due_date[$key]);
          } // if
        } // foreach
      } // if

      /**
       * Rearrange data for overview of the day in past
       */
      if ($is_overview_in_past && is_foreachable($due_date)) {
        $due_in_past = array();
      	$due_in_past['objects_completed'] = array();
        $due_in_past['objects_active'] = array();

    	  foreach ($due_date as $due_today_item) {
    	  	if (is_array($due_today_item['objects_completed'])) {
    	  	  $due_in_past['objects_completed'] = array_merge($due_in_past['objects_completed'], $due_today_item['objects_completed']);
      	  } // if

          if (is_array($due_today_item['objects_active'])) {
      	    $due_in_past['objects_active'] = array_merge($due_in_past['objects_active'], $due_today_item['objects_active']);
      	  } // if
        } // foreach

      	$due_date = &$due_in_past;
      } // if

      /**
       * Time and expenses
       */
      $timetracking_available = AngieApplication::isModuleLoaded('tracking');
      $currenices_map = null;
      if ($timetracking_available && !$is_overview_in_future) {
        $currenices_map = Currencies::getIdDetailsMap();
        
        $report = new TrackingReport();
        $report_attributes = array(
          'date_filter' => TrackingReport::DATE_FILTER_SELECTED_DATE,
          'date_on' => $this->due_date->toMySQL(),
          'group_by' => TrackingReport::GROUP_BY_PROJECT,
          'user_filter' => TrackingReport::USER_FILTER_SELECTED,
          'user_ids' => array($this->selected_user->getId()),
        );

        // at the moment report doesnt take into account selected projects only
        // but lets keep this block for future
        if ($this->getFilterProjects() == self::FILTER_PROJECTS_SELECTED) {
          $report_attributes['project_filter'] = TrackingReport::PROJECT_FILTER_SELECTED;
          $report_attributes['project_ids'] = $this->getSelectedProjectIds();
        } // if

        $report->setAttributes($report_attributes);

        try {
          $records = $report->run($user);
        } catch(DataFilterConditionsError $e) {
          $records = null;
        } catch(Exception $e) {
          throw $e;
        } // try

        $this->summarizeTimeAndExpenses($records, $total_time, $total_expenses);
      } // if

      $template->assign(array(
        'widget' => $this,
        'widget_id' => $widget_id,
        'user'  => $user,
        'widget_data' => array(
          'selected_user' => $this->selected_user,
          'due_date' => $this->due_date,
          'due_date_described' => trim($this->filter_option_day[$this->getFilterDay()], " ..."),
          'projects' => $this->projects,
          'timetracking_available' => $timetracking_available,
          'currencies_map' => $currenices_map,
          'total_time' => $total_time,
          'total_expenses' => $total_expenses,
          'is_overview_in_future' => $is_overview_in_future,
          'is_overview_today' => $is_overview_today,
          'is_overview_in_past' => $is_overview_in_past,
          'due_today' => $due_date
         ),
      ));

      return $template->fetch();
    } // renderBody

    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------

    /**
     * Returns true if this widget has additional options
     *
     * @return boolean
     */
    function hasOptions() {
      return true;
    } // hasOptions

    /**
     * Render widget options form section
     *
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $template = SmartyForAngie::getInstance()->createTemplate(get_view_path('/homescreen_widgets/day_overview_options', null, SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));

      $template->assign(array(
        'widget'    => $this,
        'widget_id' => $this->getId(),
        'user'      => $user,
        'widget_data' => array(
          'user' => $user,
          'projects' => Projects::getIdNameMap($user, STATE_TRASHED, null, null, true),
          'select_options' => $this->getSelectOptions(),
          'filter_day' => $this->getFilterDay(),
          'filter_user' => $this->getFilterUser(),
          'filter_projects' => $this->getFilterProjects(),
          'selected_date' => $this->getSelectedDate(),
          'selected_user_id' => $this->getSelectedUserId(),
          'selected_project_ids' => $this->getSelectedProjectIds(),
        )
      ));

      return $template->fetch();
    } // renderOptions

    /**
     * Summarize total hours and expenses
     *
     * @param array $result
     * @param float $total_time
     * @param array $total_expenses
     * @return void
     */
    function summarizeTimeAndExpenses($result, &$total_time, &$total_expenses) {
      if (is_foreachable($result)) {
       foreach ($result as $project_id => $project) {
         if (($this->getFilterProjects() == self::FILTER_PROJECTS_ALL || in_array($project_id, $this->getSelectedProjectIds()))) {
           foreach ($project['records'] as $record) {
             if ($record['type'] == 'TimeRecord') {
               $total_time += $record['value'];
             } else {
               if (isset($total_expenses[$record['currency_id']])) {
                 $total_expenses[$record['currency_id']] += $record['value'];
               } else {
                 $total_expenses[$record['currency_id']] = $record['value'];
               } // if
             } // if
           } // foreach
         } // if
       } // foreach
      } // if
    } // summarizeTimeAndExpenses

    // ---------------------------------------
    //  Getters and setters
    // ---------------------------------------

    /**
     * Bulk set widget attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      // day filter
      if (array_key_exists('filter_day', $attributes)) {
        $filter_day = array_var($attributes, 'filter_day', self::FILTER_DAY_TODAY, true);
        $this->setFilterDay($filter_day);

        if ($filter_day == 'selected_date' && array_key_exists(self::FILTER_DAY_SELECTED_DATE, $attributes)) {
          $this->setSelectedDate(array_var($attributes, 'selected_date', null, true));
        } // if
      } // if

      // user filter
      if (array_key_exists('filter_user', $attributes)) {
        $filter_user = array_var($attributes, 'filter_user', self::FILTER_USER_LOGGED, true);
        $this->setFilterUser($filter_user);

        if ($filter_user == self::FILTER_USER_SELECTED && array_key_exists('selected_user_id', $attributes)) {
          $this->setSelectedUserId((integer) array_var($attributes, 'selected_user_id', 0, true));
        } // if
      } // if

      // projects filter
      if (array_key_exists('filter_projects', $attributes)) {
        $filter_projects = array_var($attributes, 'filter_projects', self::FILTER_PROJECTS_ALL, true);
        $this->setFilterProjects($filter_projects);

        $selected_project_ids = array_var($attributes, 'selected_project_ids', array(), true);
        if ($filter_projects == self::FILTER_PROJECTS_SELECTED && is_foreachable($selected_project_ids)) {
          $this->setSelectedProjectIds($selected_project_ids);
        } else {
          $this->setFilterProjects(self::FILTER_PROJECTS_ALL);
          $this->setSelectedProjectIds(array());
        } // if
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

    /**
     * Get the exact values for each filter option
     *
     * @param IUser $user
     * @return void
     */
    function setWidgetFilters(IUser $user) {
      $this->due_date = new DateValue();
      switch ($this->getFilterDay()) {
        case self::FILTER_DAY_LAST_BUSSINESS_DAY:
          $this->due_date->advance(-86400, true);
          while ($this->due_date->isWorkday() === false) : $this->due_date->advance(-86400, true); endwhile;
          break;
        case self::FILTER_DAY_YESTERDAY:
          $this->due_date->advance(-86400, true);
          break;
        case self::FILTER_DAY_TOMORROW:
          $this->due_date->advance(86400, true);
          break;
        case self::FILTER_DAY_NEXT_BUSINESS_DAY:
          $this->due_date->advance(86400, true);
          while ($this->due_date->isWorkday() === false) : $this->due_date->advance(86400, true); endwhile;
          break;
        case self::FILTER_DAY_SELECTED_DATE:
          $this->due_date = $this->due_date->makeFromString($this->getSelectedDate());
          break;
      } // switch filiter for day

      $this->selected_user = ($this->getFilterUser() == self::FILTER_USER_LOGGED) ? $user : Users::findById($this->getSelectedUserId());
      $this->due_date->advance(get_user_gmt_offset($user), true);

      $projects = ($this->getFilterProjects() == self::FILTER_PROJECTS_ALL) ?
                        Projects::findActiveByUser($this->selected_user) :
                        Projects::findByIds($this->getSelectedProjectIds());

      if (is_foreachable($projects)) {
        foreach ($projects as $project) {
          $this->projects[$project->getId()] = $project;
        } // foreach
      } // if

    } // setWidgetFilters

    /**
     * Get day filter
     *
     * @return string
     */
    function getFilterDay() {
      return $this->getAdditionalProperty('filter_day', self::FILTER_DAY_TODAY);
    } // getFilterDay

    /**
     * Set day filter
     *
     * @param string $filter_day
     * @return mixed
     */
    function setFilterDay($filter_day) {
      return $this->setAdditionalProperty('filter_day', $filter_day);
    } // setFilterDay

    /**
     * Get user filter
     *
     * @return string
     */
    function getFilterUser() {
      return $this->getAdditionalProperty('filter_user', self::FILTER_USER_LOGGED);
    } // getFilterUser

    /**
     * Set user filter
     *
     * @param string $filter_user
     * @return mixed
     */
    function setFilterUser($filter_user) {
      return $this->setAdditionalProperty('filter_user', $filter_user);
    } // setFilterUser

    /**
     * Get projects filter
     *
     * @return string
     */
    function getFilterProjects() {
      return $this->getAdditionalProperty('filter_projects', self::FILTER_PROJECTS_ALL);
    } // getFilterProjects

    /**
     * Set projects filter
     *
     * @param string $filter_projects
     * @return mixed
     */
    function setFilterProjects($filter_projects) {
      return $this->setAdditionalProperty('filter_projects', $filter_projects);
    } // setFilterProjects

    /**
     * Get selected day (in case when custom date is picked)
     *
     * @return string
     */
    function getSelectedDate() {
      return $this->getAdditionalProperty('selected_date', date("Y/m/d"));
    } // getSelectedDate

    /**
     * Set selected day
     *
     * @param string $selected_date
     * @return mixed
     */
    function setSelectedDate($selected_date) {
      return $this->setAdditionalProperty('selected_date', $selected_date);
    } // setSelectedDate

    /**
     * Get selected projects' IDs (in case when custom projects are picked)
     *
     * @return array
     */
    function getSelectedProjectIds() {
      return $this->getAdditionalProperty('selected_project_ids', array());
    } // getSelectedProjectIds

    /**
     * Set selected project IDs
     *
     * @param array $selected_project_ids
     * @return mixed
     */
    function setSelectedProjectIds($selected_project_ids) {
      return $this->setAdditionalProperty('selected_project_ids', $selected_project_ids);
    }

    /**
     * Get selected user ID (in case when custom user is picked)
     *
     * @return int
     */
    function getSelectedUserId() {
      return $this->getAdditionalProperty('selected_user_id', 0);
    } // getSelectedUser

    /**
     * Set selected user id
     *
     * @param int $selected_user_id
     * @return mixed
     */
    function setSelectedUserId($selected_user_id) {
      return $this->setAdditionalProperty('selected_user_id', $selected_user_id);
    } // setSelectedUserId

    /**
     * Get options for select
     *
     * @return array
     */
    function getSelectOptions() {
      return array(
        'day'       => $this->filter_option_day,
        'user'      => $this->filter_option_user,
        'projects'  => $this->filter_option_project
      );
    } // getSelectOptions

  }