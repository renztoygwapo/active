<?php

  /**
   * Tracking objects manager
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  final class TrackingObjects {
    
    /**
     * Returns true if $user can access tracking section of $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'tracking', ($check_tab ? 'time' : null));
    } // canAccess
    
    /**
     * Returns true if $user can track time and expanses in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'tracking', ($check_tab ? 'time' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage time and expenses in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'tracking', ($check_tab ? 'time' : null));
    } // canManage

    /**
     * Returns true if user can log time/expenses as other user
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    static function canTrackForOthers(User $user, Project $project) {
      return $user->isProjectManager() || $project->getLeaderId() == $user->getId() || $user->projects()->getPermission('tracking', $project) >= ProjectRole::PERMISSION_MANAGE;
    } // canTrackForOthers
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return tracking objects by parent
     *
     * @param ITracking $parent
     * @param integer $min_state
     * @return array
     */
    static function findByParent(ITracking $parent, $min_state = STATE_ARCHIVED) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_type = get_class($parent);
      $parent_id = $parent->getId();
      
      $rows = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id AS 'type_id', $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE parent_type = ? AND parent_id = ? AND state >= ?) UNION ALL
                           (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, $expenses_table.category_id AS type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE parent_type = ? AND parent_id = ? AND state >= ?) ORDER BY record_date DESC, created_on DESC", $parent_type, $parent_id, $min_state, $parent_type, $parent_id, $min_state);
      
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          if($row['type'] == 'TimeRecord') {
            $item = new TimeRecord();
            $row['job_type_id'] = $row['type_id'];
          } else {
            $item = new Expense();
            $row['category_id'] = $row['type_id'];
          } // if
          
          unset($row['type_id']);
          
          $item->loadFromRow($row);
          
          $result[] = $item;
        } // foreach
        
        return $result;
      } else {
        return null;
      } // if
    } // findByParent

    /**
     * Return tracking objects by parent as array
     *
     * @param IUser $user
     * @param ITracking $parent
     * @param integer $min_state
     * @return array
     */
    static function findByParentAsArray($user, ITracking $parent, $min_state = STATE_ARCHIVED) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';

      $parent_type = get_class($parent);
      $parent_id = $parent->getId();
      $parent_project = $parent->getProject();

      $rows = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id AS 'type_id', $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE parent_type = ? AND parent_id = ? AND state >= ?) UNION ALL
                           (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, $expenses_table.category_id AS type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE parent_type = ? AND parent_id = ? AND state >= ?) ORDER BY record_date DESC, created_on DESC", $parent_type, $parent_id, $min_state, $parent_type, $parent_id, $min_state);

      if($rows instanceof DBResult) {

        // get needed users
        $user_ids = DB::executeFirstColumn("(SELECT $time_records_table.user_id FROM $time_records_table WHERE parent_type = '$parent_type' AND parent_id = $parent_id AND state >= $min_state) UNION (SELECT $expenses_table.user_id FROM $expenses_table WHERE parent_type = '$parent_type' AND parent_id = $parent_id AND state >= $min_state)");
        $users = Users::getIdDetailsMap($user_ids, array('first_name', 'last_name', 'email', 'company_id'));
        $user_url = Router::assemble('people_company_user', array('company_id' => '--COMPANY--ID--', 'user_id' => '--USER--ID--'));

        $time_records_usage_data = array();
        $expenses_usage_data = array();

        if(AngieApplication::isModuleLoaded('invoicing')) {
          $time_records_data = DB::execute('SELECT parent_id FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE parent_type = ?', 'TimeRecord');
          if ($time_records_data instanceof DBResult) {
            $time_records_usage_data = $time_records_data->toArrayIndexedBy('parent_id');
          } // if

          $expenses_data = DB::execute('SELECT parent_id FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE parent_type = ?', 'Expense');
          if ($expenses_data instanceof DBResult) {
            $expenses_usage_data = $expenses_data->toArrayIndexedBy('parent_id');
          } // if
        } // if

        $can_manage_tracking_records_in_project = TrackingObjects::canManage($user, $parent_project);
        $now_timestamp = DateTimeValue::now()->getTimestamp();

        $result = array();

        foreach($rows as $tracking_object) {
          if($tracking_object['type'] == 'TimeRecord') {
            $usage_data = isset($time_records_usage_data[$tracking_object['id']]) ? $time_records_usage_data[$tracking_object['id']] : null;
          } else {
            $usage_data = isset($expenses_usage_data[$tracking_object['id']]) ? $expenses_usage_data[$tracking_object['id']] : null;
          } // if

          $can_edit = self::canEditByTrackingObject($tracking_object, $user, $usage_data, $parent_project, $can_manage_tracking_records_in_project, $now_timestamp);
          $can_trash = $can_edit && ($tracking_object['state'] !== STATE_TRASHED);

          $summarized_tracking_object = array(
            'id' => (int) $tracking_object['id'],
            'class' => $tracking_object['type'],
            'parent_type' => $tracking_object['parent_type'],
            'parent_id' => (int) $tracking_object['parent_id'],
            'state' => (int) $tracking_object['state'],
            'value' => floatval($tracking_object['value']),
            'user_id' => (int) $tracking_object['user_id'],
            'user_name' => $tracking_object['user_name'],
            'user_email' => $tracking_object['user_email'],
            'summary' => $tracking_object['summary'],
            'record_date' => new DateValue($tracking_object['record_date']),
            'billable_status' => (int) $tracking_object['billable_status'],
            'created_on' => new DateTimeValue($tracking_object['created_on']),
            'created_by_id' => (int) $tracking_object['created_by_id'],
            'created_by_name' => $tracking_object['created_by_name'],
            'created_by_email' => $tracking_object['created_by_email'],
            'permissions' => array(
              'can_edit' => $can_edit,
              'can_trash' => $can_trash
            ),
            'urls' => self::getEditAndTrashUrlByArrayTrackingObject($tracking_object)
          );

          if($tracking_object['type'] == 'TimeRecord') {
            $summarized_tracking_object['job_type_name'] = JobTypes::getNameById($tracking_object['type_id']);
          } else {
            $summarized_tracking_object['category_name'] = ExpenseCategories::getNameById($tracking_object['type_id']);
            $summarized_tracking_object['currency'] = $parent instanceof Project ? $parent->getProject()->getCurrency() : $parent->getProject()->getCurrency();
          } // if

          // Add view URL for mobile devices
          if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE) {
            $summarized_tracking_object['urls']['view'] = self::getViewUrlByArrayTrackingObject($tracking_object);
          } // if

          $object_user = array_var($users, $tracking_object['user_id']);
          if($object_user) {
            $display_name_params = array(
              'first_name' => array_var($object_user, 'first_name'),
              'last_name' => array_var($object_user, 'last_name'),
              'email' => array_var($object_user, 'email')
            );

            $summarized_tracking_object['user'] = array(
              'id' => $object_user['id'],
              'display_name' => Users::getUserDisplayName($display_name_params),
              'short_display_name' => Users::getUserDisplayName($display_name_params, true),
              'permalink' => str_replace(array('--COMPANY--ID--', '--USER--ID--'), array($object_user['company_id'], $object_user['id']), $user_url)
            );
          } else {
            $display_name_params = array(
              'full_name' => $tracking_object['user_name'],
              'email' => $tracking_object['user_email']
            );

            $summarized_tracking_object['user'] = array(
              'display_name' => Users::getUserDisplayName($display_name_params),
              'short_display_name' => Users::getUserDisplayName($display_name_params, true),
              'permalink' => 'mailto:' . $tracking_object['user_email']
            );
          } // if

          $result[] = $summarized_tracking_object;
        } // foreach

        return $result;
      } // if

      return null;
    } // findByParentAsArray
    
    /**
     * Return tracking items by project
     *
     * @param IUser $user
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findByProject(IUser $user, Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter($user, $project, true, $min_state, $min_visibility);
      
      $result = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
                   		       (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on DESC");
      
      if($result instanceof DBResult) {
        $result->returnObjectsByField('type');
      } // if
      
      return $result;
    } // findByProject

    /**
     * Find recent tracking records for a given project
     *
     * @param IUser $user
     * @param ITracking|Project|Task $parent
     * @param int $min_state
     * @param int $min_visibility
     * @param int $limit
     * @return TrackingObject[]
     */
    static function findRecent(IUser $user, ITracking $parent, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL, $limit = 300) {
      if($limit === null) {
        $limit_string = '';
      } else {
        $limit = (integer) $limit;

        if($limit < 1) {
          $limit = 300;
        } // if

        $limit_string = "LIMIT 0, $limit";
      } // if

      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';

      $parent_conditions = self::prepareParentTypeFilter($user, $parent, true, $min_state, $min_visibility);

      $result = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
                   		       (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on DESC $limit_string");

      if($result instanceof DBResult) {
        $result->returnObjectsByField('type');
      } // if

      return $result;
    } // findRecent
    
    /**
     * Return all tracked objects for a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findAllByProject(Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter(null, $project, true, $min_state, $min_visibility);
      
      $result = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
                   		       (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on DESC");
      
      if($result instanceof DBResult) {
        $result->returnObjectsByField('type');
      } // if
      
      return $result;
    } // findAllByProject
    
    /**
     * Return list of tracking object IDs for a given project
     * 
     * Id's are grouped by tracking object type
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array|null
     */
    static function findIdsByProject(Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter(null, $project, true, $min_state, $min_visibility);
      
      $records = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
                              (SELECT 'Expense' AS 'type', $expenses_table.id FROM $expenses_table WHERE $parent_conditions AND state >= $min_state)");
      
      $result = array(
        'TimeRecord' => array(), 
        'Expense' => array(), 
      );
      
      if($records) {
        foreach($records as $record) {
          $result[$record['type']][] = (integer) $record['id'];
        } // foreach
      } // if
      
      return $result;
    } // findIdsByProject

    /**
     * Return tracking object totals for a given project
     *
     * @param User $user
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array|null
     */
    static function findTotalsByProject(User $user, Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';

      $parent_conditions = self::prepareParentTypeFilter($user, $project, true, $min_state, $min_visibility);

      $init_values = array(
        'TimeRecord' => 0.00,
        'Expense' => 0.00
      );

      $totals = array(
        'this_week' => $init_values,
        'this_month' => $init_values,
        'previous_week' => $init_values,
        'previous_month' => $init_values,
        'all_time' => $init_values,
      );

      if(is_foreachable($totals)) {
        $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today

        foreach($totals as $column => $values) {
          $column_conditions = "";

          switch($column) {
            case 'this_week':
              $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);

              $week_start =  $today->beginningOfWeek($first_week_day)->toMySQL();
              $week_end =  $today->endOfWeek($first_week_day)->toMySQL();

              $column_conditions = '(record_date >= ' . DB::escape($week_start) . ' AND record_date <= ' . DB::escape($week_end) . ')';
              break;

            case 'this_month':
              $month_start = DateValue::beginningOfMonth($today->getMonth(), $today->getYear())->toMySQL();
              $month_end = DateValue::endOfMonth($today->getMonth(), $today->getYear())->toMySQL();

              $column_conditions = '(record_date >= ' . DB::escape($month_start) . ' AND record_date <= ' . DB::escape($month_end) . ')';
              break;

            case 'previous_week':
              $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);

              $last_week = $today->advance(-604800, false);

              $week_start = $last_week->beginningOfWeek($first_week_day)->toMySQL();
              $week_end = $last_week->endOfWeek($first_week_day)->toMySQL();

              $column_conditions = '(record_date >= ' . DB::escape($week_start) . ' AND record_date <= ' . DB::escape($week_end) . ')';
              break;

            case 'previous_month':
              $month = $today->getMonth() - 1;
              $year = $today->getYear();

              if($month == 0) {
                $month = 12;
                $year -= 1;
              } // if

              $month_start = DateValue::beginningOfMonth($month, $year)->toMySQL();
              $month_end = DateValue::endOfMonth($month, $year)->toMySQL();

              $column_conditions = '(record_date >= ' . DB::escape($month_start) . ' AND record_date <= ' . DB::escape($month_end) . ')';
              break;
          } // switch

          if($column_conditions)
            $column_conditions = $column_conditions . " AND ";

          $tracking_objects = DB::execute("(SELECT 'TimeRecord' AS 'type', SUM($time_records_table.value) AS 'sum_value' FROM $time_records_table WHERE $column_conditions $parent_conditions AND state >= $min_state) UNION ALL
                                           (SELECT 'Expense' AS 'type', SUM($expenses_table.value) AS 'sum_value' FROM $expenses_table WHERE $column_conditions $parent_conditions AND state >= $min_state)");

          if($tracking_objects instanceof DBResult) {
            foreach($tracking_objects as $tracking_object) {
              $totals[$column][$tracking_object['type']] += (float) $tracking_object['sum_value'];
            } // foreach
          } // if
        } // foreach
      } // if

      return $totals;
    } // findTotalsByProject
    
    /**
     * Return amount of money spent on given project
     * 
     * Cost is calculated based on job types and expenses
     *
     * @param IUser $user
     * @param Project $project
     * @return float
     */
    static function sumCostByProject(IUser $user, Project $project) {
      $min_state = STATE_ARCHIVED;
      $min_visibility = VISIBILITY_PRIVATE;
      
      $billable = DB::escape(BILLABLE_STATUS_BILLABLE);
      
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter($user, $project, true, $min_state, $min_visibility);
      
      $rows = DB::execute("(SELECT $time_records_table.value AS 'value', $time_records_table.job_type_id AS 'job_type_id', 'TimeRecord' AS 'type' FROM $time_records_table WHERE $parent_conditions AND state >= $min_state AND billable_status >= $billable) UNION ALL
                           (SELECT $expenses_table.value AS 'value', '0' AS 'job_type_id', 'Expense' AS 'type' FROM $expenses_table WHERE $parent_conditions AND state >= $min_state AND billable_status >= $billable)");
      
      if($rows) {
        $job_types = JobTypes::getIdRateMapFor($project);
        
        $result = 0;
        
        foreach($rows as $row) {
          if($row['type'] == 'TimeRecord') {
            $job_type_id = (integer) $row['job_type_id'];
            
            if(isset($job_types[$job_type_id])) {
              $result += (float) $row['value'] * $job_types[$job_type_id];
            } // if
          } else {
            $result += $row['value'];
          } // if
        } // foreach
        
        return $result;
      } else {
        return 0;
      } // if
    } // sumCostByProject
    
    /**
     * Summarize project cost by job type (expenses are added as Other Expenses)
     * 
     * @param IUser $user
     * @param Project $project
     * @return array
     */
    static function sumCostByTypeAndProject(IUser $user, Project $project) {
      $min_state = STATE_ARCHIVED;
      $min_visibility = VISIBILITY_PRIVATE;
      
      $billable = DB::escape(BILLABLE_STATUS_BILLABLE);
      
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter($user, $project, true, $min_state, $min_visibility);
      
      $rows = DB::execute("(SELECT $time_records_table.value AS 'value', $time_records_table.job_type_id AS 'job_type_id', 'TimeRecord' AS 'type' FROM $time_records_table WHERE $parent_conditions AND state >= $min_state AND billable_status >= $billable) UNION ALL
                           (SELECT $expenses_table.value AS 'value', '0' AS 'job_type_id', 'Expense' AS 'type' FROM $expenses_table WHERE $parent_conditions AND state >= $min_state AND billable_status >= $billable)");
      
      if($rows) {
        $job_types = JobTypes::getIdRateMapFor($project);
        $job_type_names = JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE);
        
        $result = array();

        foreach($job_types as $k => $v) {
          $result[$k] = array(
            'name' => $job_type_names[$k],
            'hours' => 0,
            'value' => 0,
            'is_time' => true,
          );
        } // foreach
        
        $result['expenses'] = array(
          'name' => lang('Other Expenses'), 
          'value' => 0,
          'is_time' => false,
        );
        
        foreach($rows as $row) {
          if($row['type'] == 'TimeRecord') {
            $job_type_id = (integer) $row['job_type_id'];
            
            if(isset($job_types[$job_type_id])) {
              if(!isset($result[$job_type_id]['rate'])) {
                $result[$job_type_id]['rate'] = $job_types[$job_type_id];
              } // if

              $result[$job_type_id]['hours'] += (float) $row['value'];
              $result[$job_type_id]['value'] += (float) $row['value'] * $job_types[$job_type_id];
            } // if
          } else {
            $result['expenses']['value'] += (float) $row['value'];
          } // if
        } // foreach
        
        return $result;
      } else {
        return null;
      } // if
    } // sumCostByTypeAndProject
    
    /**
     * Returns summarized time data for given user in a given project
     *
     * @param Project $project
     * @param IUser $for
     * @param IUser $user
     * @param integer $min_state
     * @return array
     */
    static function sumUserTimeRecordsByDate(Project $project, IUser $for, IUser $user, $min_state = STATE_ARCHIVED) {
      $parent_conditions = self::prepareParentTypeFilter($user, $project);
      
      $result = array();
      
      $rows = DB::execute("SELECT record_date, SUM(value) AS 'value' FROM " . TABLE_PREFIX . "time_records WHERE $parent_conditions AND user_id = ? AND state >= ? GROUP BY record_date ORDER BY record_date", $for->getId(), $min_state);
      if($rows) {
        foreach($rows as $row) {
          $result[$row['record_date']] = (float) $row['value'];
        } // foreach
      } // if
      
      return $result;
    } // sumUserTimeRecordsByDate
    
    /**
     * Return time records logged for a given user ($for) for a given day in a 
     * given project
     *
     * @param Project $project
     * @param DateValue $day
     * @param IUser $for
     * @param IUser $user
     * @param integer $min_state
     * @return TimeRecord[]
     */
    static function findUserTimeRecordsByDate(Project $project, DateValue $day, IUser $for, IUser $user, $min_state = STATE_ARCHIVED) {
      return TimeRecords::find(array(
        'conditions' => DB::prepare('user_id = ? AND record_date = ? AND state >= ?', $for->getId(), $day, $min_state) . ' AND ' . self::prepareParentTypeFilter($user, $project),
        'order' => 'created_on DESC', 
      ));
    } // findUserTimeRecordsByDate
    
    /**
     * Return tracking items by project for time expenses log
     *
     * @param IUser $user
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findForTimeExpensesLog(IUser $user, Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $parent_conditions = self::prepareParentTypeFilter($user, $project, true, $min_state, $min_visibility);

      $result = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
                   		       (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on DESC LIMIT 0, 300");

      if($result instanceof DBResult) {

        // get needed users
        $user_ids = DB::executeFirstColumn("(SELECT $time_records_table.user_id FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION (SELECT $expenses_table.user_id FROM $expenses_table WHERE $parent_conditions AND state >= $min_state)");
        $users = Users::getIdDetailsMap($user_ids, array('first_name', 'last_name', 'email', 'company_id'));
        $user_url = Router::assemble('people_company_user', array('company_id' => '--COMPANY--ID--', 'user_id' => '--USER--ID--'));
        
        $objects = array();

          foreach ($result as $tracking_object) {
            $can_edit = self::canEditByArrayTrackingObject($tracking_object, $user);
            $can_trash = $can_edit && ($tracking_object['state'] !== STATE_TRASHED);

            $summarized_tracking_object = array(
  					'id' => (int)$tracking_object['id'],
  					'class' => $tracking_object['type'],
            'parent_type' => $tracking_object['parent_type'],
            'parent_id' => (int)$tracking_object['parent_id'],
  					'value' => floatval($tracking_object['value']),
  					'summary' => $tracking_object['summary'],
  					'record_date' => new DateValue($tracking_object['record_date']),
            'billable_status' => (int)$tracking_object['billable_status'],
        		'permissions' => array(
        			'can_edit' => $can_edit,
        			'can_trash' => $can_trash
        		),
        		'urls' => self::getEditAndTrashUrlByArrayTrackingObject($tracking_object),
  				);

            $object_user = array_var($users, $tracking_object['user_id']);
            if ($object_user) {
              $display_name_params = array(
                'first_name' => array_var($object_user, 'first_name'),
                'last_name' => array_var($object_user, 'last_name'),
                'email' => array_var($object_user, 'email')
              );

              $summarized_tracking_object['user'] = array(
                'id' => $object_user['id'],
                'display_name' => Users::getUserDisplayName($display_name_params),
                'short_display_name' => Users::getUserDisplayName($display_name_params, true),
                'permalink' => str_replace(array('--COMPANY--ID--', '--USER--ID--'), array($object_user['company_id'], $object_user['id']), $user_url)
              );
            } else {
              $display_name_params = array(
                'full_name' => $tracking_object['user_name'],
                'email' => $tracking_object['user_email']
              );

              $summarized_tracking_object['user'] = array(
                'display_name' => Users::getUserDisplayName($display_name_params),
                'short_display_name' => Users::getUserDisplayName($display_name_params, true),
                'permalink' => 'mailto:' . $tracking_object['user_email']
              );
            } // if

  				$objects[] = $summarized_tracking_object;
        } // foreach

        return $objects;
      } // if
      
      return null;
    } // findByProject

    /**
     * Find all tracking objects in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     */
    static function findForExport(Project $project, User $user, &$parents_map, $changes_since) {
      $result = array();

      if(TrackingObjects::canAccess($user, $project)) {
        $time_records_table = TABLE_PREFIX . 'time_records';
        $expenses_table = TABLE_PREFIX . 'expenses';

        $parent_conditions = self::prepareParentTypeFilter($user, $project);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND created_on > '$changes_since_date'";
        } // if

        $tracking_objects = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= ? $additional_condition) UNION ALL
      										 (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= ? $additional_condition) ORDER BY record_date DESC, created_on ASC", STATE_ARCHIVED, STATE_ARCHIVED);

        if($tracking_objects instanceof DBResult) {
          $tracking_objects->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'job_type_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'user_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'billable_status' => DBResult::CAST_INT,
            'value' => DBResult::CAST_FLOAT
          ));

          $result = $tracking_objects->toArray();
        } // if
      } // if

      return $result;
    } // findForExport

    /**
     * Find all tracking objects in project and prepare them for export
     *
     * @param Project $project
     * @param User $user
     * @param string $output_file
     * @param array $parents_map
     * @param int $changes_since
     * @return array
     * @throws Error
     */
    static function exportToFileByProject(Project $project, User $user, $output_file, &$parents_map, $changes_since) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      // Open json array
      fwrite($output_handle, '[');

      $count = 0;
      if(TrackingObjects::canAccess($user, $project)) {
        $time_records_table = TABLE_PREFIX . 'time_records';
        $expenses_table = TABLE_PREFIX . 'expenses';

        $parent_conditions = self::prepareParentTypeFilter($user, $project);

        $additional_condition = '';
        if(!is_null($changes_since)) {
          $changes_since_date = DateTimeValue::makeFromTimestamp($changes_since);
          $additional_condition = "AND (created_on > '$changes_since_date' OR updated_on > '$changes_since_date')";
        } // if

        $tracking_objects = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= ? $additional_condition) UNION ALL
      										 (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= ? $additional_condition) ORDER BY record_date DESC, created_on ASC", (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED, (boolean) $additional_condition ? STATE_TRASHED : STATE_ARCHIVED);

        if($tracking_objects instanceof DBResult) {
          $tracking_objects->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
            'job_type_id' => DBResult::CAST_INT,
            'state' => DBResult::CAST_INT,
            'user_id' => DBResult::CAST_INT,
            'created_by_id' => DBResult::CAST_INT,
            'billable_status' => DBResult::CAST_INT,
            'value' => DBResult::CAST_FLOAT
          ));

          $buffer = '';
          foreach($tracking_objects as $tracking_object) {
            if($count > 0) $buffer .= ',';

            $record = array(
              'id'                => $tracking_object['id'],
              'type'              => $tracking_object['type'],
              'parent_type'       => $tracking_object['parent_type'],
              'parent_id'         => $tracking_object['parent_id'],
              'job_type_id'       => $tracking_object['job_type_id'],
              'state'             => $tracking_object['state'],
              'record_date'       => $tracking_object['record_date'],
              'value'             => $tracking_object['value'],
              'user_id'           => $tracking_object['user_id'],
              'user_name'         => $tracking_object['user_name'],
              'user_email'        => $tracking_object['user_email'],
              'summary'           => $tracking_object['summary'],
              'billable_status'   => $tracking_object['billable_status'],
              'created_on'        => $tracking_object['created_on'],
              'created_by_id'     => $tracking_object['created_by_id'],
              'created_by_name'   => $tracking_object['created_by_name'],
              'created_by_email'  => $tracking_object['created_by_email']
            );

            if($tracking_object['type'] == 'TimeRecord') {
              $record['job_type_id'] = $tracking_object['job_type_id'];
            } else {
              $record['category_id'] = $tracking_object['job_type_id'];
            } // if

            $buffer .= JSON::encode($record);

            if($count % 15 == 0 && $count > 0) {
              fwrite($output_handle, $buffer);
              $buffer = '';
            } // if

            $count++;
          } // foreach

          if($buffer) {
            fwrite($output_handle, $buffer);
          } // if
        } // if
      } // if

      // Close json array
      fwrite($output_handle, ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $count;
    } // exportToFileByProject
    
    /**
     * Prepare tracking items by project for phone list
     *
     * @param IUser $user
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findForPhoneList(IUser $user, Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
    	$time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $result = array();
    	
      $parent_conditions = self::prepareParentTypeFilter($user, $project);
      
      $rows = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.job_type_id, $time_records_table.state, $time_records_table.original_state, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on, $time_records_table.created_by_id, $time_records_table.created_by_name, $time_records_table.created_by_email FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
      										 (SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, '0' AS job_type_id, $expenses_table.state, $expenses_table.original_state, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on, $expenses_table.created_by_id, $expenses_table.created_by_name, $expenses_table.created_by_email FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on ASC");
      
      if($rows instanceof DBResult) {
        $rows->returnObjectsByField('type');
      } // if
      
      if($rows) {
        foreach($rows as $row) {
          $result[$row->getRecordDate()->toMySQL()][] = $row;
        } // foreach
      } // if
      
      return $result;
    } // findForPhoneList

    /**
     * Return tracking objects by parent for phone list
     *
     * @param IUser $user
     * @param ITracking $parent
     * @return array
     */
    static function findForPhoneListByParent($user, ITracking $parent) {
      $result = array();

      $tracking_objects = TrackingObjects::findByParentAsArray($user, $parent);
      if(is_foreachable($tracking_objects)) {
        foreach($tracking_objects as $tracking_object) {
          $result[$tracking_object['record_date']->toMySQL()][] = $tracking_object;
        } // foreach
      } // if

      return $result;
    } // findForPhoneListByParent
    
    /**
     * Prepare tracking items by project for print list
     *
     * @param IUser $user
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findForPrintList(IUser $user, Project $project, $min_state = STATE_ARCHIVED, $min_visibility = VISIBILITY_NORMAL) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
    	
      $parent_conditions = self::prepareParentTypeFilter($user, $project);
      
      $rows = DB::execute("(SELECT 'TimeRecord' AS 'type', $time_records_table.id, $time_records_table.parent_type, $time_records_table.parent_id, $time_records_table.record_date, $time_records_table.value, $time_records_table.user_id, $time_records_table.user_name, $time_records_table.user_email, $time_records_table.summary, $time_records_table.billable_status, $time_records_table.created_on FROM $time_records_table WHERE $parent_conditions AND state >= $min_state) UNION ALL
      	(SELECT 'Expense' AS 'type', $expenses_table.id, $expenses_table.parent_type, $expenses_table.parent_id, $expenses_table.record_date, $expenses_table.value, $expenses_table.user_id, $expenses_table.user_name, $expenses_table.user_email, $expenses_table.summary, $expenses_table.billable_status, $expenses_table.created_on FROM $expenses_table WHERE $parent_conditions AND state >= $min_state) ORDER BY record_date DESC, created_on ASC LIMIT 0, 300");

      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT,
          'parent_id' => DBResult::CAST_INT,
          'user_id' => DBResult::CAST_INT,
          'billable_status' => DBResult::CAST_INT,
          'record_date' => DBResult::CAST_DATE,
        ));

        $rows = $rows->toArray();

        $task_ids = array();
        $user_ids = array();

        foreach($rows as $row) {
          if($row['parent_type'] == 'Task' && !in_array($row['parent_id'], $task_ids)) {
            $task_ids[] = $row['parent_id'];
          } // if

          if($row['user_id'] && !in_array($row['user_id'], $user_ids)) {
            $user_ids[] = $row['user_id'];
          } // if
        } // foreach

        // Get task data
        $tasks = array();
        if($task_ids) {
          $task_rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND id IN (?) AND state >= ?', 'Task', $task_ids, STATE_ARCHIVED);
          if($task_rows) {
            foreach($task_rows as $task_row) {
              $tasks[(integer) $task_row['id']] = $task_row['name'];
            } // foreach
          } // if
        } // if

        // Get user data
        $users = array();
        if($user_ids) {
          $user_rows = DB::execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE id IN (?) AND state >= ?', $user_ids, STATE_ARCHIVED);
          if($user_rows) {
            foreach($user_rows as $user_row) {
              $users[(integer) $user_row['id']] = Users::getUserDisplayName($user_row, true);
            } // foreach
          } // if
        } // if

        $project_name = $project->getName();
        foreach($rows as $k => $row) {
          $task_id = $row['parent_type'] == 'Task' ? $row['parent_id'] : null;
          $user_id = $row['user_id'];

          if($task_id && isset($tasks[$task_id])) {
            $rows[$k]['parent_name'] = $tasks[$task_id];
          } else {
            $rows[$k]['parent_name'] = $project_name;
          } // if

          if($user_id && isset($users[$user_id])) {
            $rows[$k]['user_display_name'] = $users[$user_id];
          } else {
            $rows[$k]['user_display_name'] = Users::getUserDisplayName(array(
              'full_name' => $row['user_name'],
              'email' => $row['user_email']
            ), true);
          } // if
        } // foreach
      } // if

      return group_by_date($rows, $user, 'record_date', true, false, 0);
    } // findForPhoneList
    
    // ---------------------------------------------------
    //  Type filter
    // ---------------------------------------------------
    
    /**
     * Cached parent type filters
     *
     * @var array
     */
    static private $prepared_parent_type_filters = array();
    
    /**
     * Prepare parent type filter
     * 
     * $user is optional
     * 
     * When $min_state is not provided, system will use state of parent object 
     * as minimal state
     * 
     * When $min_visibility is not provided, system will use user's min 
     * visibility (or VISIBILITY_PRIVATE when user is not provided) as min 
     * visibility
     * 
     * @param IUser $user
     * @param ITracking $parent
     * @param boolean $include_subobjects
     * @param integer $min_state
     * @param integer $min_visibility
     * @return string
     * @throws InvalidInstanceError
     */
    static function prepareParentTypeFilter($user, ITracking $parent, $include_subobjects = true, $min_state = null, $min_visibility = null) {
      if($parent instanceof Project || $parent instanceof Task) {
        $parent_type = get_class($parent);
        $parent_id = $parent->getId();

        if($min_state === null) {
          $min_state = $parent instanceof IState ? $parent->getState() : STATE_ARCHIVED;
        } // if

        if($min_visibility === null) {
          $min_visibility = $user instanceof IUser ? $user->getMinVisibility() : VISIBILITY_PRIVATE;
        } // if
        
        $cache_key = $include_subobjects ? "{$parent_id}_with_subojects_{$min_state}_{$min_visibility}" : "{$parent_id}_without_subojects_{$min_state}_{$min_visibility}";
        
        if(isset(self::$prepared_parent_type_filters[$parent_type]) && isset(self::$prepared_parent_type_filters[$parent_type][$cache_key])) {
          return self::$prepared_parent_type_filters[$parent_type][$cache_key];
        } // if
        
        $types = array($parent_type => array($parent_id));
        
        if($include_subobjects && $parent instanceof Project) {
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          
          // Tasks
          $project_objects = DB::execute("SELECT id, type FROM $project_objects_table WHERE type = 'Task' AND project_id = ? AND state >= ? AND visibility >= ?", $parent->getId(), $min_state, $min_visibility);
          
          if($project_objects) {
            foreach($project_objects as $project_object) {
              $project_object_id = (integer) $project_object['id'];
              
              if(!isset($types[$project_object['type']])) {
                $types[$project_object['type']] = array();
              } // if
              
              $types[$project_object['type']][] = $project_object_id;
            } // foreach
          } // if
        } // if
        
        // Prepare conditions
        $result = array();
        
        foreach($types as $type_name => $object_ids) {
          $result[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type_name, array_unique($object_ids));
        } // if
        
        if(isset(self::$prepared_parent_type_filters[$parent_type])) {
          self::$prepared_parent_type_filters[$parent_type][$cache_key] = '(' . implode(' OR ', $result) . ')';
        } else {
          self::$prepared_parent_type_filters[$parent_type] = array($cache_key => '(' . implode(' OR ', $result) . ')');
        } // if
        
        return self::$prepared_parent_type_filters[$parent_type][$cache_key];
      } else {
        throw new InvalidInstanceError('parent', $parent, array('Project', 'Task'));
      } // if
    } // prepareParentTypeFilter

    /**
     * Returns if user can edit tracking object
     *
     * @param $tracking_array[]
     * @param IUser $user
     * @param boolean $usage_data
     * @param Project $project
     * @param boolean $can_manage_tracking_records_in_project
     * @param integer $now_timestamp
     * @return bool
     */
    private static function canEditByTrackingObject($tracking_array, IUser $user, $usage_data, $project, $can_manage_tracking_records_in_project, $now_timestamp) {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        $is_used = $tracking_array['billable_status'] > BILLABLE_STATUS_BILLABLE && (bool) $usage_data;
      } else {
        $is_used = false;
      } // if

      if($is_used) {
        return false;
      } // if

      // Project manager, project leader or person with management permissions in time & expenses section
      if($user->isProjectManager() || $project->isLeader($user) || $can_manage_tracking_records_in_project) {
        return true;
      } // if

      // Author or person for whose account this time has been logged, editable within 30 days
      if($tracking_array['user_id'] == $user->getId() || $tracking_array['created_by_id'] == $user->getId()) {
        $created_on = new DateTimeValue($tracking_array['created_on']);
        return ($created_on->getTimestamp() + (30 * 86400)) > $now_timestamp;
      } // if

      return false;
    } // canEditByTrackingObject

    /**
     * Returns if user can edit tracking object when given array for object
     *
     * @param $tracking_array[]
     * @param IUser $user
     *
     * @return bool
     */
    private static function canEditByArrayTrackingObject($tracking_array, IUser $user) {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        $is_used = $tracking_array['billable_status'] > BILLABLE_STATUS_BILLABLE && (bool) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE parent_type = ? AND parent_id = ?', $tracking_array['type'], $tracking_array['id']);
      } else {
        $is_used = false;
      } // if
      if ($is_used) {
        return false;
      } //if

      if ($tracking_array['parent_type'] == "Project") {
        $project = Projects::findById($tracking_array['parent_id']);
      } else {
        $object = ProjectObjects::findById($tracking_array['parent_id']);
        $project = $object->getProject();
      }

      // Project manager, project leader or person with management permissions in time & expenses section
      if($user->isProjectManager() || $project->isLeader($user) || TrackingObjects::canManage($user, $project)) {
        return true;
      } // if

      if (!isset($object)) {
        $object = $project;
      } //if

      // Author or person for whose account this time has been logged, editable within 30 days
      if($object->canView($user) && ($tracking_array['user_id'] == $user->getId() || $tracking_array['created_by_id'] == $user->getId())) {
        $created_on = new DateTimeValue($tracking_array['created_on']);
        return ($created_on->getTimestamp() + (30 * 86400)) > DateTimeValue::now()->getTimestamp();
      } // if

      return false;
    } //canEditByArrayTrackingObject

    /**
     * Return view URL for given tracking object array
     *
     * @param $tracking_array[]
     * @return string[]
     */
    private static function getViewUrlByArrayTrackingObject($tracking_array) {
      $tracking_type = $tracking_array['type'] == 'TimeRecord' ? 'time_record' : 'expense';

      $parent = DataObjectPool::get($tracking_array['parent_type'], $tracking_array['parent_id']);

      $routing_context = $parent->getRoutingContext() . '_tracking_' . $tracking_type;

      $parent_context_params = $parent->getRoutingContextParams();

      $routing_context_params = is_array($parent_context_params) ? array_merge($parent_context_params, array($tracking_type . '_id' => $tracking_array['id'])) : array($tracking_type . '_id' => $tracking_array['id']);

      return Router::assemble($routing_context, $routing_context_params);
    } // getViewUrlByArrayTrackingObject

    /**
     * Returns edit and trash url when given array for tracking object
     *
     * @param $tracking_array[]
     *
     * @return string[]
     */
    private static function getEditAndTrashUrlByArrayTrackingObject($tracking_array) {
      $tracking_type = $tracking_array['type'] == 'TimeRecord' ? 'time_record' : 'expense';

      $parent = DataObjectPool::get($tracking_array['parent_type'], $tracking_array['parent_id']);

      $routing_context = $parent->getRoutingContext() . '_tracking_' . $tracking_type;

      $parent_context_params = $parent->getRoutingContextParams();

      $routing_context_params = is_array($parent_context_params) ? array_merge($parent_context_params, array($tracking_type . '_id' => $tracking_array['id'])) : array($tracking_type . '_id' => $tracking_array['id']);

      return array(
        'edit' => Router::assemble($routing_context . '_edit', $routing_context_params),
        'trash' => Router::assemble($routing_context . '_trash', $routing_context_params)
      );
    } //getEditUrlByArrayTrackingObject


    
  }