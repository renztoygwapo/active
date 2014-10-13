<?php

  /**
   * Workload report class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class WorkloadReport extends DataFilter {
  	
  	// User filter
    const USER_FILTER_MY_COMPANY = 'my_company';
    const USER_FILTER_COMPANY = 'company';
    const USER_FILTER_SELECTED = 'selected';
    
    // Date filter
    const DATE_FILTER_TODAY = 'today';
    const DATE_FILTER_THIS_WEEK = 'this_week';
    const DATE_FILTER_THIS_MONTH = 'this_month';
    
    /**
     * Run report
     *
     * @param IUser $user
     * @param array|null $additional
     * @return array
     * @throws InvalidParamError
     */
    function run(IUser $user, $additional = null) {
      if($user instanceof User) {
        $filter = new AssignmentFilter();
        
        $workload = $this->getDueAssignments($user, $filter);
        $late = $this->getLateAssignments($user, $filter);
        
        $this->matchLateAssignments($workload, $late);
        $this->calculateTimeEstimates($workload);
        $this->includeOtherUsers($workload, $user);
        
        return $workload;
      } else {
        throw new InvalidParamError('user', $user, 'User');
      } // if
    } // run

    /**
     * Return assignments that are due based on filters and given user
     *
     * @param User $user
     * @param AssignmentFilter $filter
     * @return array|null
     * @throws InvalidParamError
     */
    private function getDueAssignments(User $user, AssignmentFilter $filter) {
    	if($filter instanceof AssignmentFilter) {
    		$this->getAssignments($user, $filter);
	      
	      // Set due on filter
	      $offset = $this->getOffset();
	      $date_filter = $this->getDateFilter();
	      
	      if($offset) {
	      	// Today
	      	if($date_filter == self::DATE_FILTER_TODAY) {
		    		$date = DateValue::makeFromString($offset . 'day')->toMySQL();
			      $filter->dueOnDate($date);
			    
			    // This Week
    			} elseif($date_filter == self::DATE_FILTER_THIS_WEEK) {
    				$first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
    				
    				$from = DateValue::makeFromTimestamp(DateValue::now()->beginningOfWeek($first_week_day)->getTimestamp() + ($offset * 7 * 24 * 60 * 60))->toMySQL();
    				$to = DateValue::makeFromTimestamp(DateValue::now()->endOfWeek($first_week_day)->getTimestamp() + ($offset * 7 * 24 * 60 * 60))->toMySQL();
    				
			      $filter->dueInRange($from, $to);
			      
			    // This Month
    			} elseif($date_filter == self::DATE_FILTER_THIS_MONTH) {
    				if($offset <> 0) {
		    			$from = DateValue::makeFromString('first day of ' . $offset . ' month')->toMySQL();
		    			$to = DateValue::makeFromString('last day of ' . $offset . ' month')->toMySQL();
		    		} elseif($offset == 1) {
		    			$from = DateValue::makeFromString('first day of next month')->toMySQL();
		    			$to = DateValue::makeFromString('last day of next month')->toMySQL();
		    		} // if
		    		
		    		$filter->dueInRange($from, $to);
		    	} // if
	      } else {
	      	$filter->setDueOnFilter($date_filter);
	      } // if
	      
	      return $filter->run($user);
      } else {
        throw new InvalidParamError('filter', $filter, 'AssignmentFilter');
      } // if
    } // getDueAssignments
    
    /**
     * Return assignments that are late based on filters and given user
     *
     * @param User $user
     * @param AssignmentFilter $filter
     * @return array
     */
    private function getLateAssignments(User $user, AssignmentFilter $filter) {
    	if($filter instanceof AssignmentFilter) {
    		$this->getAssignments($user, $filter);
	      
	      // Set due on filter
	      $offset = $this->getOffset();
	      $date_filter = $this->getDateFilter();
	      
	      if($offset) {
	      	// Today
	      	if($date_filter == self::DATE_FILTER_TODAY) {
		    		$date = DateValue::makeFromString($offset . 'day')->getTimestamp();
		    		$filter->setTodayOffset($date);
			    
			    // This Week
    			} elseif($date_filter == self::DATE_FILTER_THIS_WEEK) {
    				$first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
    				
    				$from = DateValue::makeFromTimestamp(DateValue::now()->beginningOfWeek($first_week_day)->getTimestamp() + ($offset * 7 * 24 * 60 * 60))->getTimestamp();
			      $filter->setTodayOffset($from);
			      
			    // This Month
    			} elseif($date_filter == self::DATE_FILTER_THIS_MONTH) {
    				if($offset <> 0) {
		    			$from = DateValue::makeFromString('first day of ' . $offset . ' month')->getTimestamp();
		    		} elseif($offset == 1) {
		    			$from = DateValue::makeFromString('first day of next month')->getTimestamp();
		    		} // if
		    		
		    		$filter->setTodayOffset($from);
		    	} // if
	      } // if
	      
	      $filter->setDueOnFilter(AssignmentFilter::DATE_FILTER_LATE);
	      
	      return $filter->run($user);
      } else {
        throw new InvalidParamError('filter', $filter, 'AssignmentFilter');
      } // if
    } // getLateAssignments
    
    /**
     * Return all assignments based on filters
     *
     * @param User $user
     * @param AssignmentFilter $filter
     * @return array
     */
    private function getAssignments(User $user, AssignmentFilter &$filter) {
    	
  		// Set user filter
  		if($this->getUserFilter() == self::USER_FILTER_MY_COMPANY) {
  			$filter->filterByCompany($user->getCompanyId(), true);
  		} elseif($this->getUserFilter() == self::USER_FILTER_COMPANY) {
        $filter->filterByCompany($this->getUserFilterCompanyId(), true);
      } elseif($this->getUserFilter() == self::USER_FILTER_SELECTED) {
        $filter->filterByUsers($this->getUserFilterSelectedUsers(), true);
      } else {
        $filter->setUserFilter($this->getUserFilter());
      } // if
      
      // Set project filter
      if($this->getProjectFilter() == Projects::PROJECT_FILTER_CATEGORY) {
        $filter->filterByProjectCategory($this->getProjectCategoryId());
      } elseif($this->getProjectFilter() == Projects::PROJECT_FILTER_CLIENT) {
        $filter->filterByProjectClient($this->getProjectClientId());
      } elseif($this->getProjectFilter() == Projects::PROJECT_FILTER_SELECTED) {
        $filter->filterByProjects($this->getProjectIds());
      } else {
        $filter->setProjectFilter($this->getProjectFilter());
      } // if
      
      // Set include all projects, all subtasks, tracking data and other assignees filters
      $filter->setIncludeAllProjects(true);
      $filter->setIncludeSubtasks($this->getIncludeSubtasks());
      $filter->setIncludeTrackingData(true);
      $filter->setIncludeOtherAssignees(true);
      
      // Set complete on and group by filters
      $filter->setCompletedOnFilter(AssignmentFilter::DATE_FILTER_IS_NOT_SET);
      $filter->setGroupBy(AssignmentFilter::GROUP_BY_ASSIGNEE);
      
      return $filter;
    } // getAssignments
    
    /**
     * Go through due assignments and match them against late assignments
     *
     * @param array $due
     * @param array $late
     */
    private function matchLateAssignments(&$due, $late) {
      if(!is_foreachable($late)) {
      	return false;
      } // if
      
    	foreach($late as $k => $v) {
        if(is_foreachable($late[$k]['assignments'])) {
          foreach($late[$k]['assignments'] as $assignment_id => $assignment) {
          	if(!isset($due[$k])) {
          		$due[$k] = $late[$k];
          	} // if
          	
          	$due[$k]['late'][] = array(
        			'__k' => $assignment_id,
        			'__v' => $assignment
        		);
          	
          	// Remove it from due assignments array
        		if(isset($due[$k]['assignments'][$assignment_id])) {
        			unset($due[$k]['assignments'][$assignment_id]);
        		} // if
          } // foreach
        } // if
      } // foreach
      
      return true;
    } // matchLateAssignments
    
    /**
     * Go through due assignments and count estimated and tracked time
     *
     * @param array $workload
     */
    private function calculateTimeEstimates(&$workload) {
      if(!is_foreachable($workload)) {
      	return false;
      } // if
      
    	foreach($workload as $k => $v) {
      	$count_estimated_time = 0;
      	$count_tracked_time = 0;
      	
      	if(is_foreachable($workload[$k]['assignments'])) {
      		foreach($workload[$k]['assignments'] as $assignment_id => $assignment) {
      			if(isset($workload[$k]['assignments'][$assignment_id]['estimated_time']) && $workload[$k]['assignments'][$assignment_id]['estimated_time']) {
      				$count_estimated_time = $count_estimated_time + $workload[$k]['assignments'][$assignment_id]['estimated_time'];
      			} // if
      			
      			if(isset($workload[$k]['assignments'][$assignment_id]['tracked_time']) && $workload[$k]['assignments'][$assignment_id]['tracked_time']) {
      				$count_tracked_time = $count_tracked_time + $workload[$k]['assignments'][$assignment_id]['tracked_time'];
      			} // if
      		} // foreach
      	} // if
      	
      	if(is_foreachable($workload[$k]['late'])) {
      		foreach($workload[$k]['late'] as $key => $value) {
      			if(isset($workload[$k]['late'][$key]['__v']['estimated_time']) && $workload[$k]['late'][$key]['__v']['estimated_time']) {
      				$count_estimated_time = $count_estimated_time + $workload[$k]['late'][$key]['__v']['estimated_time'];
      			} // if
      			
      			if(isset($workload[$k]['late'][$key]['__v']['tracked_time']) && $workload[$k]['late'][$key]['__v']['tracked_time']) {
      				$count_tracked_time = $count_tracked_time + $workload[$k]['late'][$key]['__v']['tracked_time'];
      			} // if
      		} // foreach
      	} // if
      	
      	$workload[$k]['count_estimated_time'] = $count_estimated_time;
      	$workload[$k]['count_tracked_time'] = $count_tracked_time;
      	
      	// Maintain uniformity response
      	if(!isset($workload[$k]['late'])) {
      		$workload[$k]['late'] = array();
      	} // if
      } // foreach
      
      return true;
    } // calculateTimeEstimates
    
    /**
     * Loop through users matched by filter criteria and include them in report if they aren't already and set additional info
     *
     * @param array $workload
     * @param IUser $user
     * @return boolean
     */
    private function includeOtherUsers(&$workload, $user) {
    	$users = $this->getMatchingUsers($user);

    	if(!is_foreachable($users)) {
    		return false;
    	} // if
    	
			foreach($users as $k => $user) {
				if(!isset($workload['user-' . $user->getId()])) {
					$workload['user-' . $user->getId()] = array(
						'label' => $user->getDisplayName(),
						'assignments' => array(),
						'late' => array(),
						'count_estimated_time' => 0,
						'count_tracked_time' => 0
					);
				} // if
			} // foreach

      // Make sure to include all needed assignee data
      if(is_foreachable($workload)) {
        foreach($workload as $k => $v) {
          if($k == 'unknown-user') {
            continue;
          } // if

          $user = Users::findById(substr($k, 5));

          $workload[$k]['id'] = $user->getId();
          $workload[$k]['avatar_url'] = $user->avatar()->getUrl();
          $workload[$k]['view_url'] = $user->getViewUrl();
        } // foreach
      } // if
			
			// Make sure to move unknown user onto the end of array
			if(isset($workload['unknown-user'])) {
				$unknown_user = $workload['unknown-user'];
				unset($workload['unknown-user']);
				$workload['unknown-user'] = $unknown_user;
			} // if

  		return true;
    } // includeOtherUsers
    
    /**
     * Return users based on user filter and given user
     *
     * @param User $user
     * @return array
     */
    private function getMatchingUsers(User $user) {
    	$users = array();
    	
    	switch($this->getUserFilter()) {
    		
        // All members of logged user company
        case self::USER_FILTER_MY_COMPANY:
        	$company = Companies::findById($user->getCompanyId());
        	
    			if($company instanceof Company) {
  					$company_users = Users::findByCompany($company, $user->visibleUserIds($company));
  					
  					if(is_foreachable($company_users)) {
  						foreach($company_users as $company_user) {
  							if($company_user instanceof User) {
  								$users[] = $company_user;
  							} // if
  						} // foreach
  					} // if
    			} // if
          break;
          
        // All members of specific company
        case self::USER_FILTER_COMPANY:
        	$company = Companies::findById($this->getUserFilterCompanyId());
        	
    			if($company instanceof Company) {
  					$company_users = Users::findByCompany($company, $user->visibleUserIds($company));
  					
  					if(is_foreachable($company_users)) {
  						foreach($company_users as $company_user) {
  							if($company_user instanceof User) {
  								$users[] = $company_user;
  							} // if
  						} // foreach
  					} // if
    			} // if
          break;
          
        // Selected users
        case self::USER_FILTER_SELECTED:
        	$selected_users = Users::findByIds($this->getUserFilterSelectedUsers());
					
					if(is_foreachable($selected_users)) {
						foreach($selected_users as $selected_user) {
							if($selected_user instanceof User) {
								$users[] = $selected_user;
							} // if
						} // foreach
					} // if
          break;
    	} // switch
    	
    	return $users;
    } // getMatchingUsers
    
    /**
     * Return data so it is good for CSV export
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|void
     */
    function runForExport(IUser $user, $additional = null) {

    } // runForExport
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      // User filter
      $result['user_filter'] = $this->getUserFilter();
      switch($result['user_filter']) {
        case self::USER_FILTER_COMPANY:
          $result['company_id'] = (integer) $this->getUserFilterCompanyId();
          break;

        case self::USER_FILTER_SELECTED:
          $result['user_ids'] = $this->getUserFilterSelectedUsers();
          break;
      } // switch
      
      // Date filter
      $result['date_filter'] = $this->getDateFilter();

      // Project filter
      $result['project_filter'] = $this->getProjectFilter();
      switch($this->getProjectFilter()) {
        case Projects::PROJECT_FILTER_CATEGORY:
          $result['project_category_id'] = $this->getProjectCategoryId();
          break;
        case Projects::PROJECT_FILTER_CLIENT:
          $result['project_client_id'] = $this->getProjectClientId();
          break;
        case Projects::PROJECT_FILTER_SELECTED:
          $result['project_ids'] = $this->getProjectIds();
          break;
      } // switch

      // Include subtasks
      $result['include_subtasks'] = (boolean) $this->getIncludeSubtasks();

      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'workload_report';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('workload_report_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Getters, setters and attributes
    // ---------------------------------------------------
    
    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['user_filter'])) {
        if($attributes['user_filter'] == self::USER_FILTER_COMPANY) {
          $this->filterByCompany(array_var($attributes, 'company_id'));
        } elseif($attributes['user_filter'] == self::USER_FILTER_SELECTED) {
          $this->filterByUsers(array_var($attributes, 'user_ids'));
        } else {
          $this->setUserFilter($attributes['user_filter']);
        } // if
      } // if
      
      if(isset($attributes['date_filter'])) {
        $this->setDateFilter($attributes['date_filter']);
      } // if
      
      if(isset($attributes['offset'])) {
        $this->setOffset($attributes['offset']);
      } // if
      
      if(isset($attributes['project_filter'])) {
        if($attributes['project_filter'] == Projects::PROJECT_FILTER_CATEGORY) {
          $this->filterByProjectCategory(array_var($attributes, 'project_category_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_CLIENT) {
          $this->filterByProjectClient(array_var($attributes, 'project_client_id'));
        } elseif($attributes['project_filter'] == Projects::PROJECT_FILTER_SELECTED) {
          $this->filterByProjects(array_var($attributes, 'project_ids'));
        } else {
          $this->setProjectFilter($attributes['project_filter']);
        } // if
      } // if
      
      if(isset($attributes['include_subtasks'])) {
        $this->setIncludeSubtasks((boolean) $attributes['include_subtasks']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
  
    /**
     * Return user filter value
     *
     * @return string
     */
    function getUserFilter() {
      return $this->getAdditionalProperty('user_filter', self::USER_FILTER_MY_COMPANY);
    } // getUserFilter
    
    /**
     * Set user filter value
     *
     * @param string $value
     * @return string
     */
    function setUserFilter($value) {
      return $this->setAdditionalProperty('user_filter', $value);
    } // setUserFilter
    
    /**
     * Set filter by company values
     *
     * @param integer $company_id
     */
    function filterByCompany($company_id) {
      $this->setUserFilter(self::USER_FILTER_COMPANY);
      $this->setAdditionalProperty('company_id', $company_id);
    } // filterByCompany
    
    /**
     * Return company ID set for user filter
     *
     * @return integer
     */
    function getUserFilterCompanyId() {
      return $this->getAdditionalProperty('company_id');
    } // getUserFilterCompanyId
    
    /**
     * Set user filter to filter only tracked object for selected users
     *
     * @param array $user_ids
     */
    function filterByUsers($user_ids) {
      $this->setUserFilter(self::USER_FILTER_SELECTED);
      
      if(is_array($user_ids)) {
        foreach($user_ids as $k => $v) {
          $user_ids[$k] = (integer) $v;
        } // foreach
      } else {
        $user_ids = null;
      } // if
      
      $this->setAdditionalProperty('selected_users', $user_ids);
    } // filterByUsers
    
    /**
     * Return array of selected users
     *
     * @return array
     */
    function getUserFilterSelectedUsers() {
      return $this->getAdditionalProperty('selected_users');
    } // getUserFilterSelectedUsers
    
    /**
     * Return date filter value
     *
     * @return string
     */
    function getDateFilter() {
      return $this->getAdditionalProperty('date_filter', self::DATE_FILTER_TODAY);
    } // getDateFilter
    
    /**
     * Set date filter to a given $value
     *
     * @param string $value
     * @return string
     */
    function setDateFilter($value) {
      return $this->setAdditionalProperty('date_filter', $value);
    } // setDateFilter
    
    /**
     * Return offset value
     *
     * @return string
     */
    function getOffset() {
      return $this->getAdditionalProperty('offset', 0);
    } // getOffset
    
    /**
     * Set offset to a given $value
     *
     * @param string $value
     * @return string
     */
    function setOffset($value) {
      return $this->setAdditionalProperty('offset', $value);
    } // setOffset
    
    /**
     * Return project filter value
     *
     * @return string
     */
    function getProjectFilter() {
      return $this->getAdditionalProperty('project_filter', Projects::PROJECT_FILTER_ANY);
    } // getProjectFilter

    /**
     * Set project filter value
     *
     * @param string $value
     * @return string
     */
    function setProjectFilter($value) {
      return $this->setAdditionalProperty('project_filter', $value);
    } // setProjectFilter

    /**
     * Set filter to filter records by project category
     *
     * @param integer $project_category_id
     * @return integer
     */
    function filterByProjectCategory($project_category_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CATEGORY);
      $this->setAdditionalProperty('project_category_id', (integer) $project_category_id);
    } // filterByProjectCategory

    /**
     * Return project category ID
     *
     * @return integer
     */
    function getProjectCategoryId() {
      return (integer) $this->getAdditionalProperty('project_category_id');
    } // getProjectCategoryId

    /**
     * Set filter to filter records by project client
     *
     * @param integer $project_client_id
     * @return integer
     */
    function filterByProjectClient($project_client_id) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_CLIENT);
      $this->setAdditionalProperty('project_client_id', (integer) $project_client_id);
    } // filterByProjectClient

    /**
     * Return project client ID
     *
     * @return integer
     */
    function getProjectClientId() {
      return (integer) $this->getAdditionalProperty('project_client_id');
    } // getProjectClientId

    /**
     * Set this report to filter records by project ID-s
     *
     * @param array $project_ids
     * @return array
     */
    function filterByProjects($project_ids) {
      $this->setProjectFilter(Projects::PROJECT_FILTER_SELECTED);

      if(is_array($project_ids)) {
        foreach($project_ids as $k => $v) {
          $project_ids[$k] = (integer) $v;
        } // foreach
      } else {
        $project_ids = null;
      } // if

      $this->setAdditionalProperty('project_ids', $project_ids);
    } // filterByProjects

    /**
     * Return project ID-s
     *
     * @return array
     */
    function getProjectIds() {
      return $this->getAdditionalProperty('project_ids');
    } // getProjectIds
    
    /**
     * Returns true if this filter also matches subtasks
     *
     * @return boolean
     */
    function getIncludeSubtasks() {
      return $this->getAdditionalProperty('include_subtasks', false);
    } // getIncludeSubtasks

    /**
     * Set include subtasks flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setIncludeSubtasks($value) {
      return $this->setAdditionalProperty('include_subtasks', (boolean) $value);
    } // setIncludeSubtasks

    /**
     * Use by managers for serious reporting, so it needs to go through all projects
     *
     * @return bool
     */
    function getIncludeAllProjects() {
      return true;
    } // getIncludeAllProjects

    /**
     * Get "really" selected assignees
     *
     * @param User $user
     * @return array
     */
    function getRealAssignees($user) {
      $result = array();

      $users = $this->getMatchingUsers($user);
      if(is_foreachable($users)) {
        foreach($users as $user) {
          $result['user-' . (integer) $user->getId()] = $user;
        } // foreach
      } // if

      return $result;
    } // getRealAssignees
    
    /**
     * Get formatted time span
     *
     * @param IUser $user
     * @return string
     */
    function getTimespan($user) {
      AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
      
      $offset = $this->getOffset();
    	$date_filter = $this->getDateFilter();
    	
    	// Today
    	if($date_filter == self::DATE_FILTER_TODAY) {
    		if($offset) {
    			$date = DateValue::makeFromString($offset . 'day');
    		} else {
    			$date = DateValue::makeFromString(self::DATE_FILTER_TODAY);
    		} // if
    		
    		$timespan = smarty_modifier_date($date);
    		
    	// This Week
    	} elseif($date_filter == self::DATE_FILTER_THIS_WEEK) {
    		$first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
    		
    		if($offset) {
    			$beginning = DateValue::makeFromTimestamp(DateValue::now()->beginningOfWeek($first_week_day)->getTimestamp() + ($offset * 7 * 24 * 60 * 60));
    			$end = DateValue::makeFromTimestamp(DateValue::now()->endOfWeek($first_week_day)->getTimestamp() + ($offset * 7 * 24 * 60 * 60));
    		} else {
    			$beginning = DateValue::makeFromTimestamp(DateValue::now()->beginningOfWeek($first_week_day)->getTimestamp());
    			$end = DateValue::makeFromTimestamp(DateValue::now()->endOfWeek($first_week_day)->getTimestamp());
    		} // if
    		
    		$beginning_of_week = smarty_modifier_date($beginning);
    		$end_of_week = smarty_modifier_date($end);
    		
    		$timespan = lang(':beginning_of_week - :end_of_week', array('beginning_of_week' => $beginning_of_week, 'end_of_week' => $end_of_week));
    		
    	// This Month
    	} elseif($date_filter == self::DATE_FILTER_THIS_MONTH) {
    		if($offset <> 0) {
    			$beginning = DateValue::makeFromString('first day of ' . $offset . ' month');
    			$end = DateValue::makeFromString('last day of ' . $offset . ' month');
    		} elseif($offset == 1) {
    			$beginning = DateValue::makeFromString('first day of next month');
    			$end = DateValue::makeFromString('last day of next month');
    		} else {
    			$beginning = DateValue::makeFromString('first day of this month');
    			$end = DateValue::makeFromString('last day of this month');
    		} // if
    		
    		$beginning_of_month = smarty_modifier_date($beginning);
    		$end_of_month = smarty_modifier_date($end);
    		
    		$timespan = lang(':beginning_of_month - :end_of_month', array('beginning_of_month' => $beginning_of_month, 'end_of_month' => $end_of_month));
    	} // if
    	
    	return $timespan;
    } // getTimespan
  	
  }