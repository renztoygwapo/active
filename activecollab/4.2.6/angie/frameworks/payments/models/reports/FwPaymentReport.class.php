<?php

  /**
   * FwPaymentReport.class
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  class FwPaymentReport extends DataFilter {
    
    //Client filter
    const CLIENT_FILTER_ANYBODY = 'anybody';
    const CLIENT_FILTER_SELECTED = 'selected';
    
    // Date filters
    const DATE_FILTER_ANY = 'any';
    const DATE_FILTER_YESTERDAY = 'yesterday';
    const DATE_FILTER_TODAY = 'today';
    const DATE_FILTER_LAST_WEEK = 'last_week';
    const DATE_FILTER_THIS_WEEK = 'this_week';
    const DATE_FILTER_LAST_MONTH = 'last_month';
    const DATE_FILTER_THIS_MONTH = 'this_month';
    const DATE_FILTER_LAST_YEAR = 'last_year';
    const DATE_FILTER_THIS_YEAR = 'this_year';
    const DATE_FILTER_SELECTED_YEAR = 'selected_year'; 
    const DATE_FILTER_SELECTED_DATE = 'selected_date'; 
    const DATE_FILTER_SELECTED_RANGE = 'selected_range';

    
    //status filter
    const STATUS_FILTER = 'any';
    const STATUS_FILTER_SELECTED = 'selected';
        
    const GATEWAY_FILTER_ANY = 'any';
    const GATEWAY_FILTER_SELECTED = 'selected';
    
    // Group
    const DONT_GROUP = 'dont';
    const GROUP_BY_CLIENT = 'client';
    const GROUP_BY_DATE = 'date';
    const GROUP_BY_MONTH = 'month';
    const GROUP_BY_YEAR = 'year';

    /**
     * Run report
     *
     * @param User $user
     * @return array
     */
    function run(IUser $user, $additional = null) {
      if($user instanceof User) {
        $conditions = $this->prepareConditions($user);
        if($conditions !== false) {
          return $this->runList($user, $conditions);
        } else {
          return null;
        } // if
      } else {
        throw new InvalidParamError('user', $user, 'User');
      } // if 
    } // run
    
    /**
     * Return data so it is good for export
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|void
     */
    function runForExport(IUser $user, $additional = null) {
      $data = $this->run($user, $additional);
       
      if($data) {
        $this->beginExport(array(
          'Payment ID',
          'Amount',
          'Created On',
          'Paid On',
          'Currency Code',
          'Currency Name',
          'Client ID',
          'Client Name',
          'Invoice',
          'Project ID',
          'Project Name',
          'Payment Method',
          'Status',
          'Comment'
        ), array_var($additional, 'export_format'));

        foreach($data as $k => $v) {
          if(is_foreachable($v['records'])) {
            foreach ($v['records'] as $payment) {
             $this->exportWriteLine(array(
                $payment['id'],
                $payment['amount'],
                $payment['created_on'] instanceof DateValue ? $payment['created_on']->toMySQL() : null,
                $payment['paid_on'] instanceof DateValue ? $payment['paid_on']->toMySQL() : null,
                $payment['currency']['code'],
                $payment['currency']['name'],
                $payment['client']['id'],
                $payment['client']['name'],
                $payment['parent']['name'],
                $payment['project']['id'],
                $payment['project']['name'],
                $payment['gateway_name'],
                $payment['status'],
                $payment['comment'], 
              ));
            }//foreach
          }//if
        } // foreach

        return $this->completeExport();
      } // if

      return null;
    } // runForExport
    
     /**
     * Execute report that displays payments
     *
     * @param User $user
     * @param string $conditions
     * @return array
     */
    private function runList($user, $conditions) {
      $payments_table = TABLE_PREFIX . 'payments';
      
      if($conditions) {
        $query = "SELECT * FROM $payments_table WHERE $conditions ORDER BY paid_on desc";
      } else {
        $query = "SELECT * FROM $payments_table ORDER BY paid_on desc";
      }//if
      
      $rows = DB::execute($query);
      if($rows) {
        $rows->setCasting(array(
          'id' => DBResult::CAST_INT, 
          'parent_id' => DBResult::CAST_INT,
          'group_id' => DBResult::CAST_INT, 
          'created_on' => DBResult::CAST_DATE,
          'paid_on' => DBResult::CAST_DATE,
          'amount'	=> DBResult::CAST_FLOAT
        ));
       
        $rows = $rows->toArray();

        // Populate extra info
        $this->populateParentInfo($rows);
        $this->populateClientInfo($rows);
        $this->populateCurrencyInfo($rows);
        $this->populateGatewayInfo($rows);

        // Group by and return
        switch($this->getGroupBy()) {
          case self::GROUP_BY_DATE:
            return $this->groupByDate($rows, $user);
          case self::GROUP_BY_MONTH:
            return $this->groupByMonth($rows, $user);
          case self::GROUP_BY_YEAR:
            return $this->groupByYear($rows, $user);
          case self::GROUP_BY_CLIENT:
            return $this->groupByClient($rows, $user);
          default:
            return array(
              array(
                'label' => lang('All Payments'),
                'records' => $rows,
              )
            );
        } // if
      } // if

      return null;
    } // runList

    /**
     * Group results by date
     *
     * @param array $rows
     * @param IUser $user
     * @return array
     */
    protected function groupByDate($rows, IUser $user) {
      $result = array();

      foreach($rows as $row) {
        $created_date = $row['paid_on'] instanceof DateValue ? $row['paid_on']->formatForUser($user, 0) : lang('Unknown Date');

        if(!isset($result[$created_date])) {

          $result[$created_date] = array(
            'label' => $created_date,
            'records' => array(),
          );
        } // if

        $result[$created_date]['records'][] = $row;
      } // foreach

      return $result;
    } // groupByDate

    /**
     * Group rows by month
     *
     * @param array $rows
     * @param IUser $user
     * @return array
     */
    protected function groupByMonth($rows, IUser $user) {
      $result = array();

      $months = Globalization::getMonthNames($user->getLanguage());

      foreach($rows as $row) {

        $created_date = $row['paid_on'] instanceof DateValue ? $months[$row['paid_on']->getMonth()] . ', ' . $row['paid_on']->getYear() : lang('Unknown Month');

        if(!isset($result[$created_date])) {

          $result[$created_date] = array(
            'label' => $created_date,
            'records' => array(),
          );
        } // if

        $result[$created_date]['records'][] = $row;
      } // foreach

      return $result;
    } // groupByMonth

    /**
     * Group rows by year
     *
     * @param array $rows
     * @param IUser $user
     * @return array
     */
    protected function groupByYear($rows, IUser $user) {
      $result = array();

      foreach($rows as $row) {

        $created_date = $row['paid_on'] instanceof DateValue ? (string) $row['paid_on']->getYear() : lang('Unknown Year');
        if(!isset($result[$created_date])) {

          $result[$created_date] = array(
            'label' => $created_date,
            'records' => array(),
          );
        } // if

        $result[$created_date]['records'][] = $row;
      } // foreach

      return $result;
    } // groupByYear

    /**
     * Group rows by client
     *
     * @param array $rows
     * @param IUser $user
     * @return array
     */
    protected function groupByClient($rows, IUser $user) {
      $result = array();

      foreach($rows as $row) {
        $client = $row['client']['id'] != 0 ? $row['client']['id'] : lang('Unknown Client');

        if(!isset($result[$client])) {

          $result[$client] = array(
            'label' => $row['client']['name'] ? $row['client']['name'] : lang('Unknown Client'),
            'records' => array(),
          );
        } // if

        $result[$client]['records'][] = $row;
      } // foreach

      return $result;
    } // groupByClient
    
    /** 
     * Populate parent info
     *
     * @param array $rows
     */
    private function populateParentInfo(&$rows) {
      $invoice_url_template = Router::assemble('invoice', array('invoice_id' => '-INVOICE-ID-'));
    	$project_url_template = Router::assemble('project', array('project_slug' => '-PROJECT-SLUG-'));

      foreach($rows as &$row) {
        $row['parent'] = array(
          'name' => lang('Unknown'),
          'view_url' => '#',
        );

        $row['project'] = array(
          'id' => 0,
          'name' => lang('Unknown'),
          'view_url' => '#',
        );

        if($row['parent_type'] == 'Invoice') {
          $parent = DB::executeFirstRow('SELECT project_id, varchar_field_1 as number FROM ' . TABLE_PREFIX . 'invoice_objects WHERE id = ?', $row['parent_id']);

          if($parent) {
            $row['parent']['name'] = lang(':invoice_show_as #:invoice_num', array(
              'invoice_show_as' => Invoices::printInvoiceAs(),
              'invoice_num' => $parent['number']
            ));

            $row['parent']['view_url'] = str_replace('-INVOICE-ID-', $row['parent_id'], $invoice_url_template);
          } // if

          if($parent['project_id']) {
            $project = DB::executeFirstRow('SELECT id, name, slug FROM ' . TABLE_PREFIX . 'projects WHERE id = ? AND state >= ?', $parent['project_id'], STATE_ARCHIVED);

            if($project) {
              $row['project']['id'] = (integer) $project['id'];
              $row['project']['name'] = $project['name'];
              $row['project']['view_url'] = str_replace('-PROJECT-SLUG-', $project['slug'], $project_url_template);
            } // if
          } // if
        } // if
      } // foreach

      unset($row); // just in case
    } // populateParentInfo
    
    /** 
     * Populate gateway
     *
     * @param array $rows
     */
    private function populateGatewayInfo(&$rows) {
      foreach($rows as &$row) {
        $row['gateway_name'] = Payments::getVerbosePaymentType($row['gateway_type']);
      } // foreach

      unset($row); // just in case
    } // populateParentInfo
    
    /**
     * Populate payment client info 
     * 
     * @param array $rows
     */
    private function populateClientInfo(&$rows) {
      $company_url_template = Router::assemble('people_company', array(
    		'company_id' => '-COMPANY-ID-',
    	));
    	
      foreach($rows as &$row) {
        
        $parent_table = TABLE_PREFIX . 'invoice_objects';
        $parent = DB::executeFirstRow("SELECT company_id FROM $parent_table WHERE id = ?", $row['parent_id']);
        
        if($parent && $parent['company_id']) {
          $company_id = $parent['company_id'];
          $company_table = TABLE_PREFIX . 'companies';
          $company = DB::executeFirstRow("SELECT name FROM $company_table WHERE id = ?", $company_id);
          
          $company_name = $company['name'];
          $company_url = str_replace('-COMPANY-ID-', $company_id, $company_url_template);
          
        }//if
        
        $parent = DataObjectPool::get($row['parent_type'], $row['parent_id']);
        
        $row['client'] = array();
        if($company) {
          $row['client']['id'] = $company_id;
          $row['client']['name'] = $company_name;
          $row['client']['view_url'] = $company_url;
        }//if
     } // foreach
      unset($row); // just in case
    }//populateClientInfo
    
    /**
     * Populate payment client info 
     * 
     * @param array $rows
     */
    private function populateCurrencyInfo(&$rows) {
      foreach($rows as &$row) {
        
        $currency_table = TABLE_PREFIX . 'currencies';
        $currency = DB::executeFirstRow("SELECT name, code, decimal_spaces FROM $currency_table WHERE id = ?", $row['currency_id']);
        
        $row['currency'] = array();
        if($currency) {
          $row['currency']['name'] = $currency['name'];
          $row['currency']['code'] = $currency['code'];
          $row['currency']['decimal_spaces'] = $currency['decimal_spaces'];
        }//if
      } // foreach

      unset($row); // just in case
    }//populateClientInfo
    
    /**
     * Prepare result conditions based on report settings
     *
     * @param IUser $user
     * @return array
     */
    function prepareConditions(IUser $user) { 
     
      $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today
     
      switch($this->getDateFilter()) {

        // List time records posted for today
        case self::DATE_FILTER_TODAY:
          $conditions[] = "(CAST(paid_on as date) = " . DB::escape($today->toMySQL()) . ')';
          break;
          
        // Time tracked for yesterday
        case self::DATE_FILTER_YESTERDAY:
          $yesterday = $today->advance(-86400, false);
          $conditions[] = "(CAST(paid_on as date) = " . DB::escape($yesterday->toMySQL()) . ')';
          break;
          
        // List next week records
        case self::DATE_FILTER_LAST_WEEK:
          $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
          
          $last_week = $today->advance(-604800, false);
          
          $week_start = $last_week->beginningOfWeek($first_week_day);
          $week_end = $last_week->endOfWeek($first_week_day);
          
          $week_start_str = DB::escape($week_start->toMySQL());
          $week_end_str = DB::escape($week_end->toMySQL());
          
          $conditions[] = "(CAST(paid_on as date) >= $week_start_str AND CAST(paid_on as date) <= $week_end_str)";
          break;
          
        // List this week records
        case self::DATE_FILTER_THIS_WEEK:
          $first_week_day = (integer) ConfigOptions::getValueFor('time_first_week_day', $user);
          
          $week_start = $today->beginningOfWeek($first_week_day);
          $week_end = $today->endOfWeek($first_week_day);
          
          $week_start_str = DB::escape($week_start->toMySQL());
          $week_end_str = DB::escape($week_end->toMySQL());
          
          $conditions[] = "(CAST(paid_on as date) >= $week_start_str AND CAST(paid_on as date) <= $week_end_str)";
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
          
          $conditions[] = "(CAST(paid_on as date) >= $month_start_str AND CAST(paid_on as date) <= $month_end_str)";
          break;
          
        // Last month
        case self::DATE_FILTER_THIS_MONTH:
          $month_start = DateTimeValue::beginningOfMonth($today->getMonth(), $today->getYear());
          $month_end = DateTimeValue::endOfMonth($today->getMonth(), $today->getYear());
          
          $month_start_str = DB::escape($month_start->toMySQL());
          $month_end_str = DB::escape($month_end->toMySQL());
          
          $conditions[] = "(CAST(paid_on as date) >= $month_start_str AND CAST(paid_on as date) <= $month_end_str)";
          break;

        case self::DATE_FILTER_THIS_YEAR:
          $year = $today->getYear();
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($year) . ')';
          break;
        case self::DATE_FILTER_LAST_YEAR:
          $last_year = $today->getYear() - 1;
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($last_year) . ')';
          break;
        case self::DATE_FILTER_SELECTED_YEAR:
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($this->getFilterByYear()) . ')';
          break;
        
        // Specific date
        case self::DATE_FILTER_SELECTED_DATE:
          $date_from = $this->getDateFilterSelectedDate();
          if($date_from instanceof DateValue) {
            $date_from_str = DB::escape($date_from->toMySql());
            $conditions[] = "(CAST(paid_on as date) = $date_from_str)";
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
            
            $conditions[] = "(CAST(paid_on as date) >= $date_from_str AND CAST(paid_on as date) <= $date_to_str)";
          } else {
            throw new InvalidParamError('date_filter_from_to', null, 'Date range not selected');
          } // if
          break;
      } // switch
      
      // Status filter
      switch($this->getStatusFilter()) {
        case self::STATUS_FILTER_SELECTED:
          $conditions[] = DB::prepare("(status in (?))", $this->getStatus());
          break;
      } // switch
      
      // Client filter
      switch($this->getCompanyFilter()) {
        case self::STATUS_FILTER_SELECTED:
          $invoice_table = TABLE_PREFIX . 'invoice_objects';
          $client_id = $this->getCompanyId();
          $company_invoice_ids = DB::executeFirstColumn("SELECT id FROM $invoice_table WHERE company_id=$client_id AND type='Invoice'");
         
          $conditions[] = DB::prepare("(parent_type=? AND parent_id in (?))", 'Invoice', $company_invoice_ids);
          break;
      } // switch
      

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
      
      // Date filter
      $result['date_filter'] = $this->getDateFilter();
      switch($this->getDateFilter()) {
        case self::DATE_FILTER_SELECTED_DATE:
          $result['date_on'] = $this->getDateFilterSelectedDate() instanceof DateValue ? $this->getDateFilterSelectedDate() : null;
          break;
          
        case self::DATE_FILTER_SELECTED_RANGE:
          list($date_from, $date_to) = $this->getDateFilterSelectedRange();
          
          $result['date_from'] = $date_from instanceof DateValue ? $date_from : null;
          $result['date_to'] = $date_to instanceof DateValue ? $date_to : null;
          
          break;
        case self::DATE_FILTER_SELECTED_YEAR:
          $year = $this->getFilterByYear();
          
          $result['year'] = $year;
          break;
      } // switch
      
      // Client filter
      $result['company_filter'] = $this->getCompanyFilter();
      switch($result['company_filter']) {
        case self::CLIENT_FILTER_SELECTED:
          $result['company_id'] = $this->getCompanyId();
          break;
      } // switch
      
      
       // Status filter
      $result['payment_status_filter'] = $this->getStatusFilter();
      switch($result['payment_status_filter']) {
        case self::STATUS_FILTER_SELECTED:
          $result['payment_status_selected'] = $this->getStatus();
          break;
      } // switch
     
      $result['group_by'] = $this->getGroupBy();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'payments_report';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('payments_report_id' => $this->getId());
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
      
      if(isset($attributes['date_filter'])) {
        switch($attributes['date_filter']) {
          case self::DATE_FILTER_SELECTED_DATE:
            $this->filterByDate($attributes['date_on']);
            break;
          case self::DATE_FILTER_SELECTED_RANGE:
            $this->filterByRange($attributes['date_from'], $attributes['date_to']);
            break;
          case self::DATE_FILTER_SELECTED_YEAR:
            $this->filterByYear($attributes['year']);
            break;
          default:
            $this->setDateFilter($attributes['date_filter']);
        } // switch
      } // if
      
      if(isset($attributes['company_filter'])) {
        switch ($attributes['company_filter']) {
          case self::CLIENT_FILTER_SELECTED:
            $this->filterByCompany($attributes['company_id']);
            break;
          default:
            $this->setCompanyFilter($attributes['company_filter']);
        }//switch
      }//if
      
      if(isset($attributes['include_comments'])) {
        $this->setIncludeComments($attributes['include_comments']);
      }//if
      
      if(isset($attributes['payment_status_filter'])) {
        switch ($attributes['payment_status_filter']) {
          case self::STATUS_FILTER_SELECTED:
            $this->filterByStatus($attributes['payment_status_selected']);
            break;
          default:
            $this->setStatusFilter($attributes['payment_status_filter']);
        }//switch
      }//if
      
      if(isset($attributes['group_by'])) {
        $this->setGroupBy($attributes['group_by']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
  
    /**
     * Set include comments
     * 
     */
    function setIncludeComments($value) {
       return $this->setAdditionalProperty('include_comments', $value); 
    }//setIncludeComments
    
    /**
     * Get include comments
     * 
     */
    function getIncludeComments() {
       return $this->getAdditionalProperty('include_comments', false); 
    }//setIncludeComments
    
    
    /**
     * Return company filter value
     *
     * @return string
     */
    function getCompanyFilter() {
      return $this->getAdditionalProperty('company_filter', self::CLIENT_FILTER_ANYBODY);
    } // getCompanyFilter
    
    /**
     * Set company filter to a given $value
     *
     * @param string $value
     * @return string
     */
    function setCompanyFilter($value) {
      return $this->setAdditionalProperty('company_filter', $value);
    } // setCompanyFilter
    
    
    /**
     * Set filter by company values
     *
     * @param integer $company_id
     */
    function filterByCompany($company_id) {
      $this->setCompanyFilter(self::CLIENT_FILTER_SELECTED);
      $this->setAdditionalProperty('company_id', $company_id);
    } // filterByCompany
    
    /**
     * Return company value
     *
     * @return string
     */
    function getCompanyId() {
      return $this->getAdditionalProperty('company_id');
    } // getCompanyId
    
    
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
     * Filter records by year
     *
     * @param string $year
     */
    function filterByYear($year) {
      $this->setDateFilter(self::DATE_FILTER_SELECTED_YEAR);
      $this->setAdditionalProperty('date_filter_year', (string) $year);
    } // filterByRange
    
    /**
     * Get Year
     *
     * @param string $year
     */
    function getFilterByYear() {
      return $this->getAdditionalProperty('date_filter_year');
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
     * Return status filter value
     *
     * @return string
     */
    function getStatusFilter() {
      return $this->getAdditionalProperty('payment_status_filter', self::STATUS_FILTER);
    } // getStatusFilter
    
    /**
     * Set status filter to a given value
     *
     * @param string $value
     * @return string
     */
    function setStatusFilter($value) {
      return $this->setAdditionalProperty('payment_status_filter', $value);
    } // setStatusFilter
    
    /**
     * Set filter by status values
     *
     * @param integer $company_id
     */
    function filterByStatus($value) {
      $this->setStatusFilter(self::STATUS_FILTER_SELECTED);
      $this->setAdditionalProperty('payment_status_selected', $value);
    } // filterByStatus
    
    /**
     * Return status value
     *
     * @return string
     */
    function getStatus() {
      return $this->getAdditionalProperty('payment_status_selected');
    } // getStatusFilter
    
    
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
      if($value == self::GROUP_BY_DATE || $value == self::GROUP_BY_CLIENT || $value == self::GROUP_BY_MONTH || $value == self::GROUP_BY_YEAR) {
        return $this->setAdditionalProperty('group_by', $value);
      } else {
        return $this->setAdditionalProperty('group_by', self::DONT_GROUP);
      } // if
    } // setGroupBy
    
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can edit this payment report
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isFinancialManager();
    } // canEdit
    
    /**
     * Returns true if $user can delete this tracking report
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isFinancialManager();
    } // canDelete
   
  }