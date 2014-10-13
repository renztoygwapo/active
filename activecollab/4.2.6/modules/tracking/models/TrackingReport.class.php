<?php

  /**
   * TrackingReport class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackingReport extends DataFilter implements IInvoiceBasedOn {
    
    // User filter
    const USER_FILTER_ANYBODY = 'anybody';
    const USER_FILTER_LOGGED_USER = 'logged_user';
    const USER_FILTER_COMPANY = 'company';
    const USER_FILTER_SELECTED = 'selected';
    
    // Date filters
    const DATE_FILTER_ANY = 'any';
    const DATE_FILTER_YESTERDAY = 'yesterday';
    const DATE_FILTER_TODAY = 'today';
    const DATE_FILTER_LAST_WEEK = 'last_week';
    const DATE_FILTER_THIS_WEEK = 'this_week';
    const DATE_FILTER_LAST_MONTH = 'last_month';
    const DATE_FILTER_THIS_MONTH = 'this_month';
    const DATE_FILTER_SELECTED_DATE = 'selected_date';
    const DATE_FILTER_SELECTED_RANGE = 'selected_range';
    
    // Project filter
    const PROJECT_FILTER_ANY = 'any';
    const PROJECT_FILTER_ACTIVE = 'active';
    const PROJECT_FILTER_COMPLETED = 'completed';
    const PROJECT_FILTER_CATEGORY = 'category';
    const PROJECT_FILTER_CLIENT = 'client';
    const PROJECT_FILTER_SELECTED = 'selected';
    
    // Billable filter
    const BILLABLE_FILTER_ALL = 'all';
    const BILLABLE_FILTER_BILLABLE = 'billable';
    const BILLABLE_FILTER_NOT_BILLABLE = 'not_billable';
    const BILLABLE_FILTER_BILLABLE_PAID = 'billable_paid';
    const BILLABLE_FILTER_BILLABLE_NOT_PAID = 'billable_not_paid';
    const BILLABLE_FILTER_BILLABLE_PENDING_OR_PAID = 'billable_pending_or_paid';
    const BILLABLE_FILTER_PENDING_PAYMENT = 'pending_payment';
    
    // Type filter
    const TYPE_FILTER_ANY = 'any';
    const TYPE_FILTER_TIME = 'time';
    const TYPE_FILTER_EXPENSES = 'expenses';
    
    // Job type filter
    const JOB_TYPE_FILTER_ANY = 'any';
    const JOB_TYPE_FILTER_SELECTED = 'selected';
    
    // Expenses categories filter
    const EXPENSE_CATEGORY_FILTER_ANY = 'any';
    const EXPENSE_CATEGORY_FILTER_SELECTED = 'selected';
    
    // Group
    const DONT_GROUP = 'dont';
    const GROUP_BY_DATE = 'date';
    const GROUP_BY_PROJECT = 'project';
    const GROUP_BY_PROJECT_CLIENT = 'project_client';
    
    /**
     * Return company
     *
     * If all records have one company use it, otherwise use the first that isn't owner
     *
     * @param User $user
     * @return Company
     */
    function getCompany(User $user) {
      $this->setGroupBy(TrackingReport::DONT_GROUP);
      $this->setSumByUser(false);
      
      $results = $this->run($user);

      $client_ids = array();
      
      if(is_foreachable($results)) {
        $records = $results[0]['records'];
        if(is_foreachable($records)) {
          foreach($records as $record) {
            $client_ids[] = $record['client_id'];
          }//foreach
        }//if
      }//if
      
      $unique_client_ids = array_unique($client_ids);
      
      if(is_foreachable($unique_client_ids)) {
        if(count($unique_client_ids) == 1) {
          return Companies::findById($unique_client_ids[0]);
        } else {
          foreach($unique_client_ids as $client_id) {
            $company = Companies::findById($client_id);
            if(!$company->isOwner()) {
              return $company;
            }//if
          }//foreach
        }//if
      }//if
      
      return Companies::findOwnerCompany();
    } // getCompany
    
    /**
     * Return report total time 
     * 
     * @param $user
     * @param $status
     * @return float
     */
    function getTotalTime(IUser $user, $status = null) {
      $this->setGroupBy(TrackingReport::DONT_GROUP);
      $this->setSumByUser(false);
      
      $results = $this->run($user);
      
      $total = 0;
      
      if(is_foreachable($results)) {
        $records = $results[0]['records'];
        if(is_foreachable($records)) {
          foreach($records as $record) {
            if($record['type'] == 'TimeRecord') {
              if($status) {
                if($record['billable_status'] == $status) {
                  $total += $record['value'];
                }//if
              } else {
                $total += $record['value'];
              }//if
            }//if
          }//foreach
        }//if
      }//if
      
      return $total;
    } // getTotalTime
    
    /**
     * Return report total expenses 
     * 
     * @param $user
     * @param $status
     * @return float
     */
    function getTotalExpenses(IUser $user, $status = null) {
      $this->setGroupBy(TrackingReport::DONT_GROUP);
      $this->setSumByUser(false);
      
      $results = $this->run($user);
      
      $currency_map = Currencies::getIdDetailsMap();
      if(is_foreachable($results)) {
        $records = $results[0]['records'];
        $total = array();

        if(is_foreachable($records)) {
          foreach($records as $record) {
            if($record['type'] == 'Expense') {
              if($status) {
                if($record['billable_status'] == $status) {
                  $total[$currency_map[$record['currency_id']]['code']] += $record['value'];
                }//if
              } else {
                $total[$currency_map[$record['currency_id']]['code']] += $record['value'];
              }//if
            }//if
          }//foreach
        }//if
      } else {
        $total = null;
      }//if
      
      return $total;
    } // getTotalExpenses
    
    /**
     * Run the report
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|null
     * @throws InvalidParamError
     */
    function run(IUser $user, $additional = null) {
      if($user instanceof User) {
        $conditions = $this->prepareConditions($user);
        
        if($conditions !== false) {
          if($this->getSumByUser()) {
            return $this->runSummarized($user, $conditions);
          } else {
            return $this->runList($user, $conditions);
          } // if
        } else {
          return null;
        } // if
      } else {
        throw new InvalidParamError('user', $user, 'User');
      } // if
    } // run
    
    /**
     * Run report and export content to temporal CSV file
     * 
     * @param IUser $user
     * @param array $additional
     * @return string
     * @throws InvalidInstanceError
     */
    function runForExport(IUser $user, $additional = null) {
      if($user instanceof User) {
        $conditions = $this->prepareConditions($user);

        if($this->getSumByUser()) {
          return $this->exportSummarized($this->runSummarized($user, $conditions), array_var($additional, 'export_format'));

        } else {
          $old_group_by = $this->getGroupBy();
          $this->setGroupBy(self::DONT_GROUP);

          $result = $this->runList($user, $conditions);

          $this->setGroupBy($old_group_by);

          return $this->exportAllRecords($result, array_var($additional, 'export_format'));
        } // if
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // runForExport

    /**
     * Prepare and export summarized data
     *
     * @param array $result
     * @param string $format
     * @return string
     */
    private function exportSummarized($result, $format = DataFilter::EXPORT_CSV) {
      $currencies = Currencies::getIdDetailsMap();

      switch($this->getGroupBy()) {
        case self::GROUP_BY_DATE:
          $columns = array(lang('Date'), lang('Date, Formatted'));
          break;
        case self::GROUP_BY_PROJECT:
          $columns = array(lang('Project ID'), lang('Project Name'));
          break;
        case self::GROUP_BY_PROJECT_CLIENT:
          $columns = array(lang('Client ID'), lang('Client Name'));
          break;
        default:
          $columns = array();
      } // switch

      $columns[] = 'User ID';
      $columns[] = 'User Name';
      $columns[] = 'User Email';

      if($this->queryTimeRecords()) {
        $columns[] = 'Time';
      } // if

      if($this->queryExpenses()) {
        foreach($currencies as $details) {
          $columns[] = 'Expenses (' . $details['code'] . ')';
        } // if
      } // if

      $this->beginExport($columns, $format);

      if($result) {
        foreach($result as $group_name => $group_data) {
          if(isset($group_data['records']) && is_array($group_data['records'])) {
            foreach($group_data['records'] as $record) {
              if($this->getGroupBy() != self::DONT_GROUP) {
                $csv_record = array($group_name, $group_data['label']);
              } else {
                $csv_record = array();
              } // if

              $csv_record[] = $record['user_id'];
              $csv_record[] = $record['user_name'];
              $csv_record[] = $record['user_email'];

              if($this->queryTimeRecords()) {
                $csv_record[] = $record['time'];
              } // if

              if($this->queryExpenses()) {
                foreach($currencies as $currency_id => $details) {
                  $csv_record[] = $record["expenses_for_{$currency_id}"];
                } // foreach
              } // if

              $this->exportWriteLine($csv_record);
            } // if
          } // if
        } // foreach
      } // if

      return $this->completeExport();
    } // exportSummarized

    /**
     * Prepare and export all records returned by this report
     *
     * @param array $result
     * @param string $format
     * @return string
     */
    private function exportAllRecords($result, $format = DataFilter::EXPORT_CSV) {
      $this->beginExport(array(
        'Type',
        'Record ID',
        'Group ID',
        'Group Name',
        'Parent Type',
        'Parent ID',
        'Parent Name',
        'Project ID',
        'Project Name',
        'Client ID',
        'Client Name',
        'Record Date',
        'User ID',
        'User Name',
        'User Email',
        'Summary',
        'Value',
        'Status',
      ), $format);

      if($result) {
        foreach($result as $group_name => $group_data) {
          if(isset($group_data['records']) && is_array($group_data['records'])) {
            foreach($group_data['records'] as $record) {
              switch($record['billable_status']) {
                case BILLABLE_STATUS_NOT_BILLABLE:
                  $status = 'Not Billable'; break;
                case BILLABLE_STATUS_BILLABLE:
                  $status = 'Billable'; break;
                case BILLABLE_STATUS_PENDING_PAYMENT:
                  $status = 'Pending Payment'; break;
                case BILLABLE_STATUS_PAID:
                  $status = 'Paid'; break;
                default:
                  $status = 'Unknown';
              } // if

              $this->exportWriteLine(array(
                $record['type'],
                $record['id'],
                $record['group_id'],
                $record['group_name'],
                $record['parent_type'],
                $record['parent_id'],
                $record['parent_name'],
                $record['project_id'],
                $record['project_name'],
                $record['client_id'],
                $record['client_name'],
                $record['record_date'] instanceof DateValue ? $record['record_date']->toMySQL() : null,
                $record['user_id'],
                $record['user_name'],
                $record['user_email'],
                $record['summary'],
                $record['value'],
                $status,
              ));
            } // if
          } // if
        } // foreach
      } // if

      return $this->completeExport();
    } // exportAllRecords
    
    /**
     * Execute report that displays records, instead of summarized data
     *
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runList($user, $conditions) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      $queries = array();
        
      if($this->queryTimeRecords()) {
        if($this->getJobTypeFilter() == self::JOB_TYPE_FILTER_SELECTED) {
          $queries[] = DB::prepare("(SELECT id, 'TimeRecord' AS 'type', parent_type, parent_id, job_type_id AS 'group_id', record_date, user_id, user_name, user_email, summary, value, billable_status FROM $time_records_table WHERE $conditions AND job_type_id IN (?) ORDER BY record_date DESC)", $this->getJobTypeIds());
        } else {
          $queries[] = "(SELECT id, 'TimeRecord' AS 'type', parent_type, parent_id, job_type_id AS 'group_id', record_date, user_id, user_name, user_email, summary, value, billable_status FROM $time_records_table WHERE $conditions ORDER BY record_date DESC)";
        } // if
      } // if
      
      if($this->queryExpenses()) {
        if($this->getExpenseCategoryFilter() == self::EXPENSE_CATEGORY_FILTER_SELECTED) {
          $queries[] = DB::prepare("(SELECT id, 'Expense' AS 'type', parent_type, parent_id, category_id AS 'group_id', record_date, user_id, user_name, user_email, summary, value, billable_status FROM $expenses_table WHERE $conditions AND category_id IN (?) ORDER BY record_date DESC)", $this->getExpenseCategoryIds());
        } else {
          $queries[] = "(SELECT id, 'Expense' AS 'type', parent_type, parent_id, category_id AS 'group_id', record_date, user_id, user_name, user_email, summary, value, billable_status FROM $expenses_table WHERE $conditions ORDER BY record_date DESC)";
        } // if
      } // if
      
      $rows = count($queries) == 1 ? DB::execute($queries[0]) : DB::execute(implode(' UNION ALL ', $queries));
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT, 
          'parent_id' => DBResult::CAST_INT,
          'group_id' => DBResult::CAST_INT, 
          'user_id' => DBResult::CAST_INT, 
          'record_date' => DBResult::CAST_DATE, 
          'billable_status' => DBResult::CAST_INT, 
          'value' => DBResult::CAST_FLOAT, 
        ));
        
        $rows = $rows->toArray();
        $this->populateProjectInfo($rows);
        $this->populateParentInfo($rows);
        
        $user_ids = array();
        foreach($rows as $row) {
          if($row['user_id']) {
            $user_ids[] = $row['user_id'];
          } // if
        } // foreach
        
        $users = Users::getIdNameMap($user_ids);
        
        $job_types = $this->queryTimeRecords() ? JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE) : array();
        $expense_categories = $this->queryExpenses() ? ExpenseCategories::getIdNameMap() : array();
        
        // Populate date that's general for all grouping methods
        foreach($rows as &$row) {
          if($row['user_id'] && $users && isset($users[$row['user_id']])) {
            $row['user_name'] = $user_name = $users[$row['user_id']];
          } else {
            $row['user_name'] = $row['user_name'] ? $row['user_name'] : $row['user_email'];
          } // if
          
          if($row['type'] == 'TimeRecord') {
            $row['group_name'] = $row['group_id'] && isset($job_types[$row['group_id']]) ? $job_types[$row['group_id']] : '';
          } else {
            $row['group_name'] = $row['group_id'] && isset($expense_categories[$row['group_id']]) ? $expense_categories[$row['group_id']] : '';
          } // if
        } // foreach

        unset($row); // break the reference

        $records = $this->groupRecordsForList($user, $rows);
        $this->calculateTotalsForList($records);

        return $records;
      } // if

      return null;
    } // runList

    /**
     * Return grouped records for display in the list
     *
     * @param IUser $user
     * @param array $rows
     * @return array
     */
    private function groupRecordsForList(IUser $user, $rows) {
      switch($this->getGroupBy()) {
        case self::GROUP_BY_DATE:
          return $this->groupByDateForList($user, $rows);
          break;
        case self::GROUP_BY_PROJECT:
          return $this->groupByProjectForList($user, $rows);
          break;
        case self::GROUP_BY_PROJECT_CLIENT:
          return $this->groupByProjectClientForList($user, $rows);
          break;
        default:
          return $this->groupUngroupedForList($user, $rows);
      } // switch
    } // groupRecordsForList

    /**
     * Group records by date for list
     *
     * @param IUser $user
     * @param array $rows
     * @return array
     */
    private function groupByDateForList(IUser $user, $rows) {
      $result = array();

      foreach($rows as $row) {
        if($row['record_date'] instanceof DateValue) {
          $key = $row['record_date']->toMySQL();
          $record_date = $row['record_date']->formatForUser($user, 0);
        } else {
          $key = EMPTY_DATE;
          $record_date = lang('Unknown Date');
        } // if

        if(!isset($result[$key])) {
          $result[$key] = array(
            'label' => $record_date,
            'records' => array(),
          );
        } // if

        $result[$key]['records'][] = $row;
      } // foreach

      krsort($result);

      return $result;
    } // groupByDateForList

    /**
     * Group records by project
     *
     * @param IUser $user
     * @param array $rows
     * @return array
     */
    private function groupByProjectForList(IUser $user, $rows) {
      $result = array();

      foreach($rows as $row) {
        if(!isset($result[$row['project_id']])) {
          $result[$row['project_id']] = array(
            'label' => trim($row['project_name']),
            'records' => array(),
          );
        } // if

        $result[$row['project_id']]['records'][] = $row;
      } // foreach

      uasort($result, function($a, $b) {
        return strcmp(strtolower($a['label']), strtolower($b['label']));
      });

      return $result;
    } // groupByProjectForList

    /**
     * Group by project client for list display
     *
     * @param IUser $user
     * @param array $rows
     * @return array
     */
    private function groupByProjectClientForList(IUser $user, $rows) {
      $result = array();

      $this->populateProjectClientInfo($rows);

      foreach($rows as $row) {
        if(!isset($result[$row['client_id']])) {
          $result[$row['client_id']] = array(
            'label' => trim($row['client_name']),
            'records' => array(),
          );
        } // if

        $result[$row['client_id']]['records'][] = $row;
      } // foreach

      uasort($result, function($a, $b) {
        return strcmp(strtolower($a['label']), strtolower($b['label']));
      });

      return $result;
    } // groupByProjectClientForList

    /**
     * Prepare ungrouped result for result
     *
     * @param IUser $user
     * @param array $rows
     * @return array
     */
    private function groupUngroupedForList(IUser $user, $rows) {
      return array(
        array(
          'label' => 'All Records',
          'records' => $rows,
        )
      );
    } // groupUngroupedForList

    /**
     * Calculate totals for list
     *
     * @param array $records
     */
    private function calculateTotalsForList(&$records) {
      $currencies = Currencies::find();

      foreach($records as $group_key => $group) {
        $records[$group_key]['total_time'] = 0;
        $records[$group_key]['total_expenses'] = array();
        $records[$group_key]['has_expenses'] = false;

        foreach($currencies as $currency) {
          $records[$group_key]['total_expenses'][$currency->getId()] = array(
            'value' => 0,
            'verbose' => '0',
          );
        } // foreach

        foreach($group['records'] as $record) {
          if($record['type'] == 'TimeRecord') {
            $records[$group_key]['total_time'] += $record['value'];
          } else {
            $currency_id = (integer) $record['currency_id'];

            $records[$group_key]['total_expenses'][$currency_id]['value'] += $record['value'];
          } // if
        } // foreach

        foreach($currencies as $currency) {
          $value = $records[$group_key]['total_expenses'][$currency->getId()]['value'];

          if($value) {
            $records[$group_key]['has_expenses'] = true;
            $records[$group_key]['total_expenses'][$currency->getId()]['verbose'] = $currency->format($value, null, true);
          } // if
        } // foreach
      } // foreach
    } // calculateTotalsForList

    // ---------------------------------------------------
    //  Summarized report
    // ---------------------------------------------------
    
    /**
     * Execute records summarized by user
     *
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runSummarized(User $user, $conditions) {
      if($this->getGroupBy() == self::GROUP_BY_DATE) {
        return $this->runSummarizedGroupedByDate($user, $conditions);
      } elseif($this->getGroupBy() == self::GROUP_BY_PROJECT) {
        return $this->runSummarizedGroupedByProject($user, $conditions);
      } elseif($this->getGroupBy() == self::GROUP_BY_PROJECT_CLIENT) {
        return $this->runSummarizedGroupedByProjectClient($user, $conditions);
      } else {
        return $this->runSummarizedButNotGrouped($user, $conditions);
      } // if
    } // runSummarized

    /**
     * Run summarized report where records are grouped by date
     *
     * @param User $user
     * @param array $conditions
     * @return array
     */
    private function runSummarizedGroupedByDate(User $user, $conditions) {
      $result = array();

      $rows = DB::execute($this->getQueryForSummarizedReport($user, $conditions));
      if($rows instanceof DBResult) {
        $rows->setCasting(array(
          'user_id' => DBResult::CAST_INT, 
          'record_date' => DBResult::CAST_DATE, 
          'value' => DBResult::CAST_FLOAT, 
        ));
        
        $user_ids = array();
        foreach($rows as $row) {
          if($row['user_id']) {
            $user_ids[] = (integer) $row['user_id'];
          } // if
        } // foreach
        
        $users = Users::getIdNameMap($user_ids);
        
        foreach($rows as $row) {
          $record_date = $row['record_date'] instanceof DateValue ? $row['record_date']->format("Y-m-d") : '0000-00-00';
          $currency_id = $row['currency_id'];
          
          if(!isset($result[$record_date])) {
            $result[$record_date] = array(
              'label' => $row['record_date'] instanceof DateValue ? $row['record_date']->formatForUser($user, 0) : lang('Unknown Date'),
              'records' => array(), 
            );
          } // if
          
          // Update row that's already been added (we maybe added time or expenses, but now need to set the other attribute)
          if(isset($result[$record_date]['records'][$row['user_email']])) {
            if($this->queryTimeRecords() && $row['type'] == 'TimeRecord') {
              $result[$record_date]['records'][$row['user_email']]['time'] = $row['value'];
            } elseif($this->queryExpenses() && $row['type'] == 'Expense') {
              $result[$record_date]['records'][$row['user_email']]["expenses_for_{$currency_id}"] = $row['value'];
            } // if
            
          // New row
          } else {
            if($row['user_id'] && $users && isset($users[$row['user_id']])) {
              $user_name = $users[$row['user_id']];
            } else {
              $user_name = $row['user_name'] ? $row['user_name'] : $row['user_email'];
            } // if
            
            $result[$record_date]['records'][$row['user_email']] = array(
              'user_id' => (integer) $row['user_id'], 
              'user_name' => $user_name, 
              'user_email' => $row['user_email'], 
            );
            
            if($this->queryTimeRecords()) {
              $result[$record_date]['records'][$row['user_email']]['time'] = $row['type'] == 'TimeRecord' ? $row['value'] : 0;
            } // if
            
            if($this->queryExpenses()) {
              $this->prepareCurrencyExpenseColumns($result[$record_date]['records'][$row['user_email']]);
              
              if($row['type'] == 'Expense') {
                $result[$record_date]['records'][$row['user_email']]["expenses_for_{$currency_id}"] = $row['value'];
              } // if
            } // if
          } // if
        } // foreach
        
        krsort($result);
      } // if

      $this->sortUsersInSummarizedResult($result);
      $this->calculateSumarizedTotals($result);
      
      return $result;
    } // runSummarizedGroupedByDate
    
    /**
     * Run summarized report where records are grouped by project
     * 
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runSummarizedGroupedByProject(User $user, $conditions) {
      $result = array();
      
      $rows = DB::execute($this->getQueryForSummarizedReport($user, $conditions));
      if($rows instanceof DBResult) {
        $rows->setCasting(array(
          'parent_id' => DBResult::CAST_INT, 
          'user_id' => DBResult::CAST_INT, 
          'value' => DBResult::CAST_FLOAT, 
        ));
        
        $rows = $rows->toArray();
        
        $this->populateProjectInfo($rows);
        
        $user_ids = array();
        foreach($rows as $row) {
          if($row['user_id']) {
            $user_ids[] = $row['user_id'];
          } // if
        } // foreach
        
        $users = Users::getIdNameMap($user_ids);
        
        foreach($rows as &$row) {
          $project_id = (integer) $row['project_id'];
          $currency_id = $row['currency_id'];
          
          if(!isset($result[$project_id])) {
            $result[$project_id] = array(
              'label' => $row['project_name'] ? $row['project_name'] : lang('Unknown Project'), 
              'records' => array(), 
            );
          } // if
          
          // Update existing
          if(isset($result[$project_id]['records'][$row['user_email']])) {
            if($this->queryTimeRecords() && $row['type'] == 'TimeRecord') {
              $result[$project_id]['records'][$row['user_email']]['time'] += $row['value'];
            } elseif($this->queryExpenses() && $row['type'] == 'Expense') {
              $result[$project_id]['records'][$row['user_email']]["expenses_for_{$currency_id}"] += $row['value'];
            } // if
            
          // New record
          } else {
            if($row['user_id'] && $users && isset($users[$row['user_id']])) {
              $user_name = $users[$row['user_id']];
            } else {
              $user_name = $row['user_name'] ? $row['user_name'] : $row['user_email'];
            } // if
            
            $result[$project_id]['records'][$row['user_email']] = array(
              'user_id' => (integer) $row['user_id'], 
              'user_name' => $user_name, 
              'user_email' => $row['user_email'], 
            );
            
            if($this->queryTimeRecords()) {
              $result[$project_id]['records'][$row['user_email']]['time'] = $row['type'] == 'TimeRecord' ? (float) $row['value'] : 0;
            } // if
            
            if($this->queryExpenses()) {
              $this->prepareCurrencyExpenseColumns($result[$project_id]['records'][$row['user_email']]);
              
              if($row['type'] == 'Expense') {
                $result[$project_id]['records'][$row['user_email']]["expenses_for_{$currency_id}"] = (float) $row['value'];
              } // if
            } // if
          } // if
        } // foreach

        unset($row); // destroy the reference
      } // if

      $this->sortUsersInSummarizedResult($result);
      $this->calculateSumarizedTotals($result);
      
      return $result;
    } // runSummarizedGroupedByProject
    
    /**
     * Run summarized report where records are grouped by project client
     * 
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runSummarizedGroupedByProjectClient(User $user, $conditions) {
      $result = array();
      
      $rows = DB::execute($this->getQueryForSummarizedReport($user, $conditions));
      if($rows instanceof DBResult) {
        $rows->setCasting(array(
          'parent_id' => DBResult::CAST_INT, 
          'user_id' => DBResult::CAST_INT, 
          'value' => DBResult::CAST_FLOAT, 
        ));
        
        $rows = $rows->toArray();
        
        $this->populateProjectInfo($rows);
        
        $user_ids = array();
        foreach($rows as $row) {
          if($row['user_id']) {
            $user_ids[] = $row['user_id'];
          } // if
        } // foreach
        
        $users = Users::getIdNameMap($user_ids);
        
        $this->populateProjectClientInfo($rows);
            
        foreach($rows as $row) {
          $currency_id = $row['currency_id'];

          $client_id = (integer) $row['client_id'];
          
          if(!isset($result[$client_id])) {
            $result[$client_id] = array(
              'label' => $row['client_name'], 
              'records' => array(), 
            );
          } // if
          
          // Update existing
          if(isset($result[$client_id]['records'][$row['user_email']])) {
            if($this->queryTimeRecords() && $row['type'] == 'TimeRecord') {
              $result[$client_id]['records'][$row['user_email']]['time'] += (float) $row['value'];
            } elseif($this->queryExpenses() && $row['type'] == 'Expense') {
              $result[$client_id]['records'][$row['user_email']]["expenses_for_{$currency_id}"] += (float) $row['value'];
            } // if
            
          // New record
          } else {
            if($row['user_id'] && $users && isset($users[$row['user_id']])) {
              $user_name = $users[$row['user_id']];
            } else {
              $user_name = $row['user_name'] ? $row['user_name'] : $row['user_email'];
            } // if
            
            $result[$client_id]['records'][$row['user_email']] = array(
              'user_id' => (integer) $row['user_id'], 
              'user_name' => $user_name, 
              'user_email' => $row['user_email'], 
            );
            
            if($this->queryTimeRecords()) {
              $result[$client_id]['records'][$row['user_email']]['time'] = $row['type'] == 'TimeRecord' ? (float) $row['value'] : 0;
            } // if
            
            if($this->queryExpenses()) {
              $this->prepareCurrencyExpenseColumns($result[$client_id]['records'][$row['user_email']]);
              
              if($row['type'] == 'Expense') {
                $result[$client_id]['records'][$row['user_email']]["expenses_for_{$currency_id}"] = (float) $row['value'];
              } // if
            } // if
          } // if
        } // foreach
      } // if

      $this->sortUsersInSummarizedResult($result);
      $this->calculateSumarizedTotals($result);
      
      return $result;
    } // runSummarizedGroupedByProjectClient
    
    /**
     * Run summarized report where records are not grouped
     * 
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runSummarizedButNotGrouped(User $user, $conditions) {
      $result = array(
        'all' => array(
          'label' => lang('All Matched Records'), 
          'records' => array(), 
        ), 
      );
      
      $rows = DB::execute($this->getQueryForSummarizedReport($user, $conditions));
      if($rows) {
        $rows->setCasting(array(
          'parent_id' => DBResult::CAST_INT, 
          'user_id' => DBResult::CAST_INT, 
          'value' => DBResult::CAST_FLOAT, 
        ));
        
        $rows = $rows->toArray();
        
        $user_ids = array();
        foreach($rows as $row) {
          if($row['user_id']) {
            $user_ids[] = (integer) $row['user_id'];
          } // if
        } // foreach
        
        $users = Users::getIdNameMap($user_ids);
        
        foreach($rows as $row) {
          $currency_id = $row['currency_id'];
          
          // Update row that's already been added
          if(isset($result['all']['records'][$row['user_email']])) {
            if($this->queryTimeRecords() && $row['type'] == 'TimeRecord') {
              $result['all']['records'][$row['user_email']]['time'] = $row['value'];
            } elseif($this->queryExpenses() && $row['type'] == 'Expense') {
              $result['all']['records'][$row['user_email']]["expenses_for_{$currency_id}"] = $row['value'];
            } // if
            
          // New row
          } else {
            if($row['user_id'] && isset($users[$row['user_id']])) {
              $user_name = $users[$row['user_id']];
            } else {
              $user_name = $row['user_name'] ? $row['user_name'] : $row['user_email'];
            } // if
            
            $result['all']['records'][$row['user_email']] = array(
              'user_id' => (integer) $row['user_id'], 
              'user_name' => $user_name, 
              'user_email' => $row['user_email'], 
            );
            
            if($this->queryTimeRecords()) {
              $result['all']['records'][$row['user_email']]['time'] = $row['type'] == 'TimeRecord' ? $row['value'] : 0;
            } // if
            
            if($this->queryExpenses()) {
              $this->prepareCurrencyExpenseColumns($result['all']['records'][$row['user_email']]);
              
              if($row['type'] == 'Expense') {
                $result['all']['records'][$row['user_email']]["expenses_for_{$currency_id}"] = $row['value'];
              } // if
            } // if
          } // if
        } // forech
      } // if

      $this->sortUsersInSummarizedResult($result);
      $this->calculateSumarizedTotals($result);
      
      return $result;
    } // runSummarizedButNotGrouped

    /**
     * Sort users in sumarized reports
     *
     * @param array $result
     */
    private function sortUsersInSummarizedResult(&$result) {
      foreach($result as $k => $v) {
        uasort($result[$k]['records'], function($a, $b) {
          return strcmp($a['user_name'], $b['user_name']);
        });
      } // foreach
    } // sortUsersInSummarizedResult

    /**
     * Calculate sumzarized totals
     *
     * @param array $records
     */
    private function calculateSumarizedTotals(&$records) {
      $currencies = Currencies::find();

      $currency_ids = array();
      $currency_values_empty = array();

      foreach($currencies as $currency) {
        $currency_ids[] = $currency->getId();
        $currency_values_empty[$currency->getId()] = array(
          'value' => 0,
          'verbose' => '0',
        );
      } // foreach

      foreach($records as $group_key => $group) {
        $records[$group_key]['total_time'] = 0;
        $records[$group_key]['total_expenses'] = array();
        $records[$group_key]['total_expenses'] = $currency_values_empty;

        foreach($group['records'] as $record) {
          $records[$group_key]['total_time'] += $record['time'];

          foreach($currency_ids as $currency_id) {
            $records[$group_key]['total_expenses'][$currency_id]['value'] += $record['expenses_for_' . $currency_id];
          } // foreach
        } // foreach

        foreach($currencies as $currency) {
          $value = $records[$group_key]['total_expenses'][$currency->getId()]['value'];
          $records[$group_key]['total_expenses'][$currency->getId()]['verbose'] = $currency->format($value);
        } // foreach
      } // foreach
    } // calculateSumarizedTotals
    
    /**
     * Prepare expense columns for all currencies in a given row
     * 
     * @param array $row
     */
    private function prepareCurrencyExpenseColumns(&$row) {
      $currencies = Currencies::getIdNameMap();
      
      if($currencies) {
        foreach($currencies as $currency_id => $currency_name) {
          $row["expenses_for_{$currency_id}"] = 0;
        } // foreach
      } // if
    } // prepareCurrencyExpenseColumns
    
    /**
     * Return queries for summarized reports
     *
     * @param User $user
     * @param string $conditions
     * @return array
     * @throws Exception
     */
    private function getQueryForSummarizedReport(User $user, $conditions) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      $expenses_table = TABLE_PREFIX . 'expenses';
      
      if($this->getGroupBy() == self::GROUP_BY_DATE) {
        $group_by = 'user_id, record_date';
        $extra_fields = ', record_date';
      } elseif($this->getGroupBy() == self::GROUP_BY_PROJECT || $this->getGroupBy() == self::GROUP_BY_PROJECT_CLIENT) {
        $group_by = 'user_id, parent';
        $extra_fields = ", parent_type, parent_id, CONCAT(parent_type, '-', parent_id) AS 'parent'";
      } else {
        $group_by = 'user_id';
        $extra_fields = '';
      } // if
      
      $queries = array();
        
      if($this->queryTimeRecords()) {
        if($this->getJobTypeFilter() == self::JOB_TYPE_FILTER_SELECTED) {
          $queries[] = DB::prepare("(SELECT 'TimeRecord' AS 'type', '0' AS currency_id, user_id, user_name, user_email, SUM(value) AS 'value' $extra_fields FROM $time_records_table WHERE $conditions AND job_type_id IN (?) GROUP BY $group_by ORDER BY CONCAT(user_name, user_email))", $this->getJobTypeIds());
        } else {
          $queries[] = "(SELECT 'TimeRecord' AS 'type', '0' AS currency_id, user_id, user_name, user_email, SUM(value) AS 'value' $extra_fields FROM $time_records_table WHERE $conditions GROUP BY $group_by ORDER BY CONCAT(user_name, user_email))";
        } // if
      } // if
      
      if($this->queryExpenses()) {
        $projects_table = TABLE_PREFIX . 'projects';
        $currencies = Currencies::find();
        
        foreach($currencies as $currency) {
          if($currency->getIsDefault()) {
            $additional_projects_condition = DB::prepare("$projects_table.currency_id = ? OR $projects_table.currency_id = ? OR $projects_table.currency_id IS NULL", $currency->getId(), 0);
          } else {
            $additional_projects_condition = DB::prepare("$projects_table.currency_id = ?", $currency->getId());
          } // if

          try {
            $project_ids = Projects::getProjectIdsByDataFilter($this, $user, STATE_ARCHIVED, $additional_projects_condition);
          } catch(DataFilterConditionsError $e) {
            $project_ids = null;
          } catch(Exception $e) {
            throw $e;
          } // if
          
          if(empty($project_ids)) {
            continue;
          } // if
          
          $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id IN (?) AND state >= ?', 'Task', $project_ids, STATE_ARCHIVED);
          
          if($task_ids) {
            $currency_context_conditions = DB::prepare("((parent_type = ? AND parent_id IN (?)) OR (parent_type = ? AND parent_id IN (?)))", 'Project', $project_ids, 'Task', $task_ids);
          } else {
            $currency_context_conditions = DB::prepare("(parent_type = ? AND parent_id IN (?))", 'Project', $project_ids);
          } // if
          
          $currency_id = DB::escape($currency->getId());
          
          if($this->getExpenseCategoryFilter() == self::EXPENSE_CATEGORY_FILTER_SELECTED) {
            $queries[] = DB::prepare("(SELECT 'Expense' AS 'type', $currency_id AS currency_id, user_id, user_name, user_email, SUM(value) AS 'value' $extra_fields FROM $expenses_table WHERE $conditions AND $currency_context_conditions AND category_id IN (?) GROUP BY $group_by ORDER BY CONCAT(user_name, user_email))", $this->getExpenseCategoryIds());
          } else {
            $queries[] = "(SELECT 'Expense' AS 'type', $currency_id AS currency_id, user_id, user_name, user_email, SUM(value) AS 'value' $extra_fields FROM $expenses_table WHERE $conditions AND $currency_context_conditions GROUP BY $group_by ORDER BY CONCAT(user_name, user_email))";
          } // if
        } // foreach
      } // if
      
      return implode(' UNION ', $queries);
    } // getQueriesForSummarizedReport
    
    /**
     * Go through rows and load project_id and project_name information based on 
     * parent type and parent_id information
     *
     * @param array $rows
     */
    private function populateProjectInfo(&$rows) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $project_objects = array();
      $project_ids = array();
      
      foreach($rows as &$row) {
        if(ProjectObjects::isProjectObjectClass($row['parent_type'])) {
          $row['is_project_object'] = true;
          
          $project_objects[(integer) $row['parent_id']] = false;
        } else {
          $row['is_project'] = true;
          
          $project_ids[] = (integer) $row['parent_id'];
        } // if
      } // foreach

      unset($row); // break the reference
      
      // Load project ID-s for project objects
      if(count($project_objects)) {
        $project_object_rows = DB::execute("SELECT id, name, project_id FROM $project_objects_table WHERE id IN (?)", array_unique(array_keys($project_objects)));
        if(is_foreachable($project_object_rows)) {
          foreach($project_object_rows as $project_object_row) {
            $project_objects[$project_object_row['id']] = array(
              'name' => $project_object_row['name'], 
              'project_id' => $project_object_row['project_id'], 
            );
            
            if(!in_array($project_object_row['project_id'], $project_ids)) {
              $project_ids[] = $project_object_row['project_id'];
            } // if
          } // foreach
        } // if
      } // if
      
      // Get project details
      $projects = array();
      
      $default_currency_id = Currencies::getDefaultId();
      $default_company_id = Companies::findOwnerCompany()->getId();
      $default_company_name = Companies::findOwnerCompany()->getName();
      
      $project_rows = DB::execute("SELECT id, name, company_id AS client_id, currency_id, slug FROM $projects_table WHERE id IN (?)", $project_ids);
      if($project_rows) {
        $company_names = Companies::getIdNameMap();
        
        $project_rows->setCasting(array(
          'id' => DBResult::CAST_INT, 
          'client_id' => DBResult::CAST_INT, 
          'currency_id' => DBResult::CAST_INT, 
        ));
        
        foreach($project_rows as $project_row) {
          $projects[$project_row['id']] = array(
            'name' => $project_row['name'],
            'slug' => $project_row['slug'],
            'currency_id' => $project_row['currency_id'] ? $project_row['currency_id'] : $default_currency_id, 
          );
          
          if($project_row['client_id']) {
            $projects[$project_row['id']]['client_id'] = $project_row['client_id'];
            $projects[$project_row['id']]['client_name'] = isset($company_names[$project_row['client_id']]) ? $company_names[$project_row['client_id']] : lang('Unknown');
          } else {
            $projects[$project_row['id']]['client_id'] = $default_company_id;
            $projects[$project_row['id']]['client_name'] = $default_company_name;
          } // if
        } // foreach
      } // if

      $project_url = Router::assemble('project', array('project_slug' => '--PROJECT_SLUG--'));
      
      // Now, let's populate project ID, name and currency ID fields for records
      foreach($rows as &$row) {
        $row['project_id'] = 0;
        $row['project_name'] = '--';
        $row['project_slug'] = '0';
        $row['project_url'] = '#';
        $row['client_id'] = $default_company_id;
        $row['client_name'] = $default_company_name;
        $row['currency_id'] = $default_currency_id;
        
        if(isset($row['is_project_object']) && $row['is_project_object']) {
          $project_id = $project_objects[$row['parent_id']] && isset($projects[$project_objects[$row['parent_id']]['project_id']]) ? $project_objects[$row['parent_id']]['project_id'] : 0;
        } else {
          $project_id = $row['parent_id'];
        } // if
        
        if($project_id) {
          $row['project_id'] = $project_id;
          
          if(isset($projects[$project_id])) {
            $row['project_name'] =  $projects[$project_id]['name'];
            $row['project_slug'] =  $projects[$project_id]['slug'];
            $row['project_url'] = str_replace('--PROJECT_SLUG--', $projects[$project_id]['slug'], $project_url);
            $row['currency_id'] = $projects[$project_id]['currency_id'];
            
            $row['client_id'] =  $projects[$project_id]['client_id'];
            $row['client_name'] = $projects[$project_id]['client_name'];
          } // if 
        } // if
      } // foreach

      unset($row); // in case...
    } // populateProjectInfo
    
    /**
     * Add project client information to the rows
     * 
     * project_id field is required for rows for this function to work properly
     *
     * @param array $rows
     */
    private function populateProjectClientInfo(&$rows) {
      $project_ids = array();
            
      foreach($rows as &$row) {
        if($row['project_id'] && !in_array($row['project_id'], $project_ids)) {
          $project_ids[] = $row['project_id'];
        } // if
      } // foreach
      unset($row);
      
      $projects = array();
      if(count($project_ids)) {
        $companies_table = TABLE_PREFIX . 'companies';
        $projects_table = TABLE_PREFIX . 'projects';
        
        $project_rows = DB::execute("SELECT $projects_table.id AS 'id', $projects_table.company_id AS 'client_id', $companies_table.name AS 'client_name' FROM $projects_table, $companies_table WHERE $projects_table.company_id = $companies_table.id AND $projects_table.id IN (?)", $project_ids);
        if($project_rows) {
          foreach($project_rows as $project_row) {
            $projects[$project_row['id']] = array(
              'client_id' => $project_row['client_id'], 
              'client_name' => $project_row['client_name'], 
            );
          } // foreach
        } // if
      } // if
      
      $owner_company = Companies::findOwnerCompany();
      
      foreach($rows as &$row) {
        if(isset($projects[$row['project_id']])) {
          $row['client_id'] = $projects[$row['project_id']]['client_id'];
          $row['client_name'] = $projects[$row['project_id']]['client_name'];
        } else {
          $row['client_id'] = $owner_company->getId();
          $row['client_name'] = $owner_company->getName();
        } // if
      } // foreach
      unset($row); // just in case
    } // populateProjectClientInfo
    
    /**
     * Populate parent info
     *
     * @param array $rows
     */
    private function populateParentInfo(&$rows) {
      $task_ids = array();
      
      foreach($rows as &$row) {
        if($row['parent_type'] == 'Task') {
          $task_ids[] = $row['parent_id'];
        } // if
      } // foreach
      unset($row);
      
      $tasks_info = array();

      if(count($task_ids)) {
        $info_rows = DB::execute("SELECT id, name, integer_field_1 AS 'task_id' FROM " . TABLE_PREFIX . 'project_objects WHERE type = ? AND id IN (?)', 'Task', $task_ids);
        
        if($info_rows) {
          foreach($info_rows as $info_row) {
            $tasks_info[(integer) $info_row['id']] = array(
              'name' => $info_row['name'],
              'task_id' => $info_row['task_id'],
            );
          } // foreach
        } // if
      } // if

      $task_url = AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array(
        'project_slug' => '--PROJECT_SLUG--',
        'task_id' => '--TASK_ID--',
      )) : '#';
      
      foreach($rows as &$row) {
        if($row['parent_type'] == 'Task') {
          $task_info = isset($tasks_info[$row['parent_id']]) ? $tasks_info[$row['parent_id']] : null;

          $name = $task_info ? $task_info['name'] : '';
          $url = $task_info ? str_replace(array('--PROJECT_SLUG--', '--TASK_ID--'), array($row['project_slug'], $task_info['task_id']), $task_url) : '#';
        } elseif($row['parent_type'] == 'Project') {
          $name = isset($row['project_name']) ? $row['project_name'] : '';
          $url = isset($row['project_url']) ? $row['project_url'] : '#';
        } else {
          $name = '';
          $url = '#';
        } // if
        
        $row['parent_name'] = $name;
        $row['parent_url'] = $url;
      } // foreach
      
      unset($row); // just in case
    } // populateParentInfo
    
    /**
     * Returns true if parent report queries time records table
     *
     * @return boolean
     */
    function queryTimeRecords() {
      return $this->getTypeFilter() == self::TYPE_FILTER_ANY || $this->getTypeFilter() == self::TYPE_FILTER_TIME;
    } // queryTimeRecords
    
    /**
     * Returns true if parent report queries expenses table
     *
     * @return boolean
     */
    function queryExpenses() {
      return $this->getTypeFilter() == self::TYPE_FILTER_ANY || $this->getTypeFilter() == self::TYPE_FILTER_EXPENSES;
    } // queryExpenses

    /**
     * Prepare result conditions based on report settings
     *
     * @param IUser $user
     * @return string
     * @throws InvalidParamError
     */
    function prepareConditions(IUser $user) { 
      $conditions = array(
        DB::prepare('state >= ?', STATE_ARCHIVED)
      );
      
      $project_ids = Projects::getProjectIdsByDataFilter($this, $user);
      if($project_ids) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id IN (?) AND state >= ?', 'Task', $project_ids, STATE_ARCHIVED);
        
        if($task_ids) {
          $conditions[] = DB::prepare('((parent_type = ? AND parent_id IN (?)) OR (parent_type = ? AND parent_id IN (?)))', 'Project', $project_ids, 'Task', $task_ids);
        } else {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', 'Project', $project_ids);
        } // if
      } else {
        return false; // No projects matching this report
      } // if
      
      switch($this->getUserFilter()) {
        
        // Filter for logged user
        case self::USER_FILTER_LOGGED_USER:
          $conditions[] = DB::prepare('user_id = ?', $user->getId());
          break;
          
        // Filter for selected company
        case self::USER_FILTER_COMPANY:
          $company_id = (integer) $this->getUserFilterCompanyId();
          
          if($company_id) {
            $user_ids = null;
            
            $company = Companies::findById($company_id);
            
            if($company instanceof Company) {
              $user_ids = $company->users()->getIds($user);
            } // if
            
            if($user_ids) {
              $conditions[] = DB::prepare('user_id IN (?)', $user_ids);
            } else {
              return false; // No visible users in selected company
            } // if
          } else {
            return false; // Company not found
          } // if
          
          break;
          
        // Filter for selected users
        case self::USER_FILTER_SELECTED:
          $user_ids = $this->getUserFilterSelectedUsers();
          
          if(is_foreachable($user_ids)) {
            $conditions[] = DB::prepare('user_id IN (?)', $user_ids);
          } else {
            throw new InvalidParamError('user_ids', $user_ids, 'No visible users selected');
          } // if
          
          break;
      } // switch
      
      $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today
      switch($this->getDateFilter()) {
          
        // List time records posted for today
        case self::DATE_FILTER_TODAY:
          $conditions[] = "(record_date = " . DB::escape($today->toMySQL()) . ')';
          break;
          
        // Time tracked for yesterday
        case self::DATE_FILTER_YESTERDAY:
          $yesterday = $today->advance(-86400, false);
          $conditions[] = "(record_date = " . DB::escape($yesterday->toMySQL()) . ')';
          break;
          
        // List next week records
        case self::DATE_FILTER_LAST_WEEK:
          $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
          
          $last_week = $today->advance(-604800, false);
          
          $week_start = $last_week->beginningOfWeek($first_week_day);
          $week_end = $last_week->endOfWeek($first_week_day);
          
          $week_start_str = DB::escape($week_start->toMySQL());
          $week_end_str = DB::escape($week_end->toMySQL());
          
          $conditions[] = "(record_date >= $week_start_str AND record_date <= $week_end_str)";
          break;
          
        // List this week records
        case self::DATE_FILTER_THIS_WEEK:
          $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
          
          $week_start = $today->beginningOfWeek($first_week_day);
          $week_end = $today->endOfWeek($first_week_day);
          
          $week_start_str = DB::escape($week_start->toMySQL());
          $week_end_str = DB::escape($week_end->toMySQL());
          
          $conditions[] = "(record_date >= $week_start_str AND record_date <= $week_end_str)";
          break;
          
        // List this month time records
        case self::DATE_FILTER_LAST_MONTH:
          $month = $today->getMonth() - 1;
          $year = $today->getYear();
          
          if($month == 0) {
            $month = 12;
            $year -= 1;
          } // if
          
          $month_start = DateTimeValue::beginningOfMonth($month, $year);
          $month_end = DateTimeValue::endOfMonth($month, $year);
          
          $month_start_str = DB::escape($month_start->toMySQL());
          $month_end_str = DB::escape($month_end->toMySQL());
          
          $conditions[] = "(record_date >= $month_start_str AND record_date <= $month_end_str)";
          break;
          
        // Last month
        case self::DATE_FILTER_THIS_MONTH:
          $month_start = DateTimeValue::beginningOfMonth($today->getMonth(), $today->getYear());
          $month_end = DateTimeValue::endOfMonth($today->getMonth(), $today->getYear());
          
          $month_start_str = DB::escape($month_start->toMySQL());
          $month_end_str = DB::escape($month_end->toMySQL());
          
          $conditions[] = "(record_date >= $month_start_str AND record_date <= $month_end_str)";
          break;
          
        // Specific date
        case self::DATE_FILTER_SELECTED_DATE:
          $date_from = $this->getDateFilterSelectedDate();
          if($date_from instanceof DateValue) {
            $date_from_str = DB::escape($date_from->toMySql());
            $conditions[] = "(record_date = $date_from_str)";
          } else {
            throw new InvalidParamError('date_filter_on', $date_from, 'Date not selected');
          } // if
          
          break;
          
        // Specific range
        case self::DATE_FILTER_SELECTED_RANGE:
          list($date_from, $date_to) = $this->getDateFilterSelectedRange();
          
          if($date_from instanceof DateValue && $date_to instanceof DateValue) {
            $date_from_str = DB::escape($date_from->toMySQL());
            $date_to_str = DB::escape($date_to->toMySQL());
            
            $conditions[] = "(record_date >= $date_from_str AND record_date <= $date_to_str)";
          } else {
            throw new InvalidParamError('date_filter_from_to', null, 'Date range not selected');
          } // if
          break;
      } // switch
      
      // Billable filter
      switch($this->getBillableStatusFilter()) {
        case self::BILLABLE_FILTER_BILLABLE:
          $conditions[] = DB::prepare("(billable_status = ?)", BILLABLE_STATUS_BILLABLE);
          break;
        case self::BILLABLE_FILTER_NOT_BILLABLE:
          $conditions[] = DB::prepare("(billable_status = ? OR billable_status IS NULL)", BILLABLE_STATUS_NOT_BILLABLE);
          break;
        case self::BILLABLE_FILTER_BILLABLE_PAID:
          $conditions[] = DB::prepare("(billable_status >= ?)", BILLABLE_STATUS_PAID);
          break;
        case self::BILLABLE_FILTER_PENDING_PAYMENT:
          $conditions[] = DB::prepare("(billable_status = ?)", BILLABLE_STATUS_PENDING_PAYMENT);
          break;
        case self::BILLABLE_FILTER_BILLABLE_NOT_PAID:
          $conditions[] = DB::prepare("(billable_status IN (?))", array(BILLABLE_STATUS_BILLABLE, BILLABLE_STATUS_PENDING_PAYMENT));
          break;
        case self::BILLABLE_FILTER_BILLABLE_PENDING_OR_PAID:
          $conditions[] = DB::prepare("(billable_status >= ?)", BILLABLE_STATUS_BILLABLE);
          break;
      } // switch
      
      // Make sure that we can filter by job types
      if($this->getJobTypeFilter() == self::JOB_TYPE_FILTER_SELECTED) {
        $job_type_ids = $this->getJobTypeIds();
        
        if(empty($job_type_ids)) {
          return false;
        } // if
      } // if
      
      // Make sure that we can filter by expense categories
      if($this->getExpenseCategoryFilter() == self::EXPENSE_CATEGORY_FILTER_SELECTED) {
        $expense_category_ids = $this->getExpenseCategoryIds();
        
        if(empty($expense_category_ids)) {
          return false;
        } // if
      } // if
      
      return implode(' AND ', $conditions);
    } // prepareConditions
    
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
      switch($this->getUserFilter()) {
        case TrackingReport::USER_FILTER_COMPANY:
          $result['company_id'] = (integer) $this->getUserFilterCompanyId();
          break;
          
        case TrackingReport::USER_FILTER_SELECTED:
          $result['user_ids'] = $this->getUserFilterSelectedUsers();
          break;
      } // switch
      
      // Date filter
      $result['date_filter'] = $this->getDateFilter();
      switch($this->getDateFilter()) {
        case Trackingreport::DATE_FILTER_SELECTED_DATE:
          $result['date_on'] = $this->getDateFilterSelectedDate() instanceof DateValue ? $this->getDateFilterSelectedDate() : null;
          break;
          
        case Trackingreport::DATE_FILTER_SELECTED_RANGE:
          list($date_from, $date_to) = $this->getDateFilterSelectedRange();
          
          $result['date_from'] = $date_from instanceof DateValue ? $date_from : null;
          $result['date_to'] = $date_to instanceof DateValue ? $date_to : null;
          
          break;
      } // switch
      
      // Project filter
      $result['project_filter'] = $this->getProjectFilter();
      switch($result['project_filter']) {
        case TrackingReport::PROJECT_FILTER_CATEGORY:
          $result['project_category_id'] = $this->getProjectCategoryId();
          break;
        case TrackingReport::PROJECT_FILTER_CLIENT:
          $result['project_client_id'] = $this->getProjectClientId();
          break;
        case TrackingReport::PROJECT_FILTER_SELECTED:
          $result['project_ids'] = $this->getProjectIds();
          break;
      } // switch
      
      $result['billable_status_filter'] = $this->getBillableStatusFilter();
      $result['type_filter'] = $this->getTypeFilter();
      
      $result['job_type_filter'] = $this->getJobTypeFilter();
      if($result['job_type_filter'] == self::JOB_TYPE_FILTER_SELECTED) {
        $result['job_type_ids'] = $this->getJobTypeIds();
      } // if
      
      $result['expense_category_filter'] = $this->getExpenseCategoryFilter();
      if($result['expense_category_filter'] == self::EXPENSE_CATEGORY_FILTER_SELECTED) {
        $result['expense_category_ids'] = $this->getExpenseCategoryIds();
      } // if
      
      $result['sum_by_user'] = $this->getSumByUser();
      $result['group_by'] = $this->getGroupBy();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     * @throws NotImplementedError
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
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
        switch($attributes['date_filter']) {
          case self::DATE_FILTER_SELECTED_DATE:
            $this->filterByDate($attributes['date_on']);
            break;
          case self::DATE_FILTER_SELECTED_RANGE:
            $this->filterByRange($attributes['date_from'], $attributes['date_to']);
            break;
          default:
            $this->setDateFilter($attributes['date_filter']);
        } // switch
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
      
      if(isset($attributes['type_filter'])) {
        $this->setTypeFilter($attributes['type_filter']);
      } // if
      
      if(isset($attributes['job_type_filter'])) {
        if($attributes['job_type_filter'] == self::JOB_TYPE_FILTER_SELECTED) {
          $this->filterByJobTypes(array_var($attributes, 'job_type_ids'));
        } else {
          $this->setJobTypeFilter($attributes['job_type_filter']);
        } // if
      } // if
      
      if(isset($attributes['expense_category_filter'])) {
        if($attributes['expense_category_filter'] == self::EXPENSE_CATEGORY_FILTER_SELECTED) {
          $this->filterByExpenseCategory(array_var($attributes, 'expense_category_ids'));
        } else {
          $this->setExpenseCategoryFilter($attributes['expense_category_filter']);
        } // if
      } // if
      
      if(isset($attributes['billable_status_filter'])) {
        $this->setBillableStatusFilter($attributes['billable_status_filter']);
      } // if
      
      if(isset($attributes['sum_by_user'])) {
        $this->setSumByUser((boolean) $attributes['sum_by_user']);
      } // if
      
      if(isset($attributes['group_by'])) {
        $this->setGroupBy($attributes['group_by']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
  
    /**
     * Return user filter value
     *
     * @return string
     */
    function getUserFilter() {
      return $this->getAdditionalProperty('user_filter', self::USER_FILTER_ANYBODY);
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
      return $this->getAdditionalProperty('date_filter', self::DATE_FILTER_ANY);
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
     * Filter objects tracked for a given date
     *
     * @param string $date
     */
    function filterByDate($date) {
      $this->setDateFilter(self::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('date_filter_on', (string) $date);
    } // filterByDate
    
    /**
     * Return selected date for date value
     *
     * @return DateValue
     */
    function getDateFilterSelectedDate() {
      $on = $this->getAdditionalProperty('date_filter_on');
      
      return $on ? new DateValue($on) : null;
    } // getDateFilterSelectedDate
    
    /**
     * Filter records by date range
     *
     * @param string $from
     * @param string $to
     */
    function filterByRange($from, $to) {
      $this->setDateFilter(self::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('date_filter_from', (string) $from);
      $this->setAdditionalProperty('date_filter_to', (string) $to);
    } // filterByRange
    
    /**
     * Return selected range for date filter
     *
     * @return array
     */
    function getDateFilterSelectedRange() {
      $from = $this->getAdditionalProperty('date_filter_from');
      $to = $this->getAdditionalProperty('date_filter_to');
      
      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getDateFilterSelectedRange
    
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
     * Return billable filter value
     *
     * @return string
     */
    function getBillableStatusFilter() {
      return $this->getAdditionalProperty('billable_status_filter', self::BILLABLE_FILTER_ALL);
    } // getBillableStatusFilter
    
    /**
     * Set billable filter to a given value
     *
     * @param string $value
     * @return string
     */
    function setBillableStatusFilter($value) {
      return $this->setAdditionalProperty('billable_status_filter', $value);
    } // setBillableFilter
    
    /**
     * Return type filter
     *
     * @return string
     */
    function getTypeFilter() {
      return $this->getAdditionalProperty('type_filter', self::TYPE_FILTER_ANY);
    } // getTypeFilter
    
    /**
     * Set type filter
     *
     * @param string $value
     * @return string
     */
    function setTypeFilter($value) {
      return $this->setAdditionalProperty('type_filter', $value);
    } // setTypeFilter
    
    /**
     * Return job type filter value
     * 
     * @return string
     */
    function getJobTypeFilter() {
      return $this->getAdditionalProperty('job_type_filter', self::JOB_TYPE_FILTER_ANY);
    } // getJobTypeFilter
    
    /**
     * Set job type filter value
     * 
     * @param string $value
     * @return string
     */
    function setJobTypeFilter($value) {
      return $this->setAdditionalProperty('job_type_filter', $value);
    } // setJobTypeFilter
    
    /**
     * Set job type filter to list of given values
     * 
     * @param array $job_type_ids
     * @throws InvalidParamError
     */
    function filterByJobTypes($job_type_ids) {
      if(is_array($job_type_ids)) {
        $this->setJobTypeFilter(self::JOB_TYPE_FILTER_SELECTED);
        
        foreach($job_type_ids as $k => $v) {
          $job_type_ids[$k] = (integer) $v;
        } // foreach
        
        $this->setAdditionalProperty('job_type_ids', $job_type_ids);
      } else {
        throw new InvalidParamError('job_type_ids', $job_type_ids, 'List of job type IDs should be an array');
      } // if
    } // filterByJobTypes
    
    /**
     * Return job type ID-s
     * 
     * @return array
     */
    function getJobTypeIds() {
      return $this->getAdditionalProperty('job_type_ids');
    } // getJobTypeIds
    
    /**
     * Return expense category filter value
     * 
     * @return string
     */
    function getExpenseCategoryFilter() {
      return $this->getAdditionalProperty('expense_category_filter', self::EXPENSE_CATEGORY_FILTER_ANY);
    } // getExpenseCategoryFilter
    
    /**
     * Set expense category filter value
     * 
     * @param string $value
     * @return string
     */
    function setExpenseCategoryFilter($value) {
      return $this->setAdditionalProperty('expense_category_filter', $value);
    } // setExpenseCategoryFilter
    
    /**
     * Set expense category filter to list of selected ID-s
     * 
     * @param array $expense_category_ids
     * @throws InvalidParamError
     */
    function filterByExpenseCategory($expense_category_ids) {
      if(is_array($expense_category_ids)) {
        $this->setExpenseCategoryFilter(self::EXPENSE_CATEGORY_FILTER_SELECTED);
        
        foreach($expense_category_ids as $k => $v) {
          $expense_category_ids[$k] = (integer) $v;
        } // foreach
        
        $this->setAdditionalProperty('expense_category_ids', $expense_category_ids);
      } else {
        throw new InvalidParamError('expense_category_ids', $expense_category_ids, 'List of expense category IDs should be an array');
      } // if
    } // filterByExpenseCategory
    
    /**
     * Return selected expense category ID-s
     * 
     * @return array
     */
    function getExpenseCategoryIds() {
      return $this->getAdditionalProperty('expense_category_ids');
    } // getExpenseCategoryIds
    
    /**
     * Return group by setting
     *
     * @return string
     */
    function getGroupBy() {
      return $this->getAdditionalProperty('group_by', self::DONT_GROUP);
    } // getGroupBy
    
    /**
     * Set group by value
     *
     * @param string $value
     * @return string
     */
    function setGroupBy($value) {
      if($value == self::GROUP_BY_DATE || $value == self::GROUP_BY_PROJECT || $value == self::GROUP_BY_PROJECT_CLIENT) {
        return $this->setAdditionalProperty('group_by', $value);
      } else {
        return $this->setAdditionalProperty('group_by', self::DONT_GROUP);
      } // if
    } // setGroupBy
    
    /**
     * Return sum_by_user
     *
     * @return boolean
     */
    function getSumByUser() {
    	return $this->getAdditionalProperty('sum_by_user', false);
    } // getSumByUser
    
    /**
     * Set sum_by_user
     *
     * @param boolean $value
     * @return boolean
     */
    function setSumByUser($value) {
      return $this->setAdditionalProperty('sum_by_user', (boolean) $value);
    } // setSumByUser

    /**
     * Use by managers for serious reporting, so it needs to go through all projects
     *
     * @return bool
     */
    function getIncludeAllProjects() {
      return true;
    } // getIncludeAllProjects
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can edit this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager();
    } // canEdit
    
    /**
     * Returns true if $user can delete this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager();
    } // canDelete
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'tracking_report';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('tracking_report_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return invoice implementation
     * 
     * @return IInvoiceBasedOnTrackingReportImplementation
     */
    function &invoice() {
      return $this->getDelegateInstance('invoice', function() {
        return AngieApplication::isModuleLoaded('invoicing') ? 'IInvoiceBasedOnTrackingReportImplementation' : 'IInvoiceBasedOnImplementationStub';
      });
    } //invoice
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return run report URL
     *
     * @return string
     */
    function getViewUrl() {
      if($this->isLoaded()) {
        $params = array('tracking_report_id' => $this->getId());
      } else {
        $params = $this->getReportParams();
      } // if
      
      return Router::assemble('tracking_reports', $params);
    } // getViewUrl
    
    /**
     * Return report paramters, used for GET in URL-s
     *
     * @return array
     */
    protected function getReportParams() {
      $result = array(
        'report[user_filter]' => $this->getUserFilter(), 
        'report[date_filter]' => $this->getDateFilter(), 
        'report[project_filter]' => $this->getProjectFilter(), 
        'report[billable_status_filter]' => $this->getBillableStatusFilter(), 
        'report[type_filter]' => $this->getTypeFilter(), 
        'report[sum_by_user]' => $this->getSumByUser(), 
        'report[group_by]' => $this->getGroupBy(), 
      );
      
      // User filter
      switch($this->getUserFilter()) {
        case TrackingReport::USER_FILTER_COMPANY:
          $result['report[company_id]'] = (integer) $this->getUserFilterCompanyId();
          break;
          
        case TrackingReport::USER_FILTER_SELECTED:
          $result['report[user_ids]'] = $this->getUserFilterSelectedUsers();
          break;
      } // switch
      
      // Date filter
      switch($this->getDateFilter()) {
        case Trackingreport::DATE_FILTER_SELECTED_DATE:
          $result['report[date_on]'] = $this->getDateFilterSelectedDate() instanceof DateValue ? $this->getDateFilterSelectedDate()->toMySql() : null;
          break;
          
        case Trackingreport::DATE_FILTER_SELECTED_RANGE:
          list($date_from, $date_to) = $this->getDateFilterSelectedRange();
          
          $result['report[date_from]'] = $date_from instanceof DateValue ? $date_from->toMySQL() : null;
          $result['report[date_to]'] = $date_to instanceof DateValue ? $date_to->toMySQL() : null;
          
          break;
      } // switch
      
      // Project filter
      switch($this->getProjectFilter()) {
        case TrackingReport::PROJECT_FILTER_CATEGORY:
          $result['report[project_category_id]'] = $this->getProjectCategoryId();
          break;
        case TrackingReport::PROJECT_FILTER_CLIENT:
          $result['report[project_client_id]'] = $this->getProjectClientId();
          break;
        case TrackingReport::PROJECT_FILTER_SELECTED:
          $result['report[project_ids]'] = $this->getProjectIds();
          break;
      } // switch
      
      return $result;
    } // getReportParams

    // ---------------------------------------------------
    //  Printing
    // ---------------------------------------------------

    /**
     * Add more print data, if needed for this report
     *
     * @param array $additional_print_data
     */
    function getAdditionalPrintData(&$additional_print_data) {
      $additional_print_data['currencies'] = array();

      foreach(Currencies::find() as $currency) {
        $additional_print_data['currencies'][$currency->getId()] = $currency;
      } // foreach
    } // getAdditionalPrintData
    
  }