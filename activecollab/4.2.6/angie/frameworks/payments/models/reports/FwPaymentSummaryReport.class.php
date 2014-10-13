<?php

  /**
   * Framework level payments summary report implementation
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class FwPaymentSummaryReport extends DataFilter {
    
    //Client filter
    const CLIENT_FILTER_ANYBODY = 'anybody';
    const CLIENT_FILTER_SELECTED = 'selected';
    
    // Date filters
    const YEAR_FILTER_ANY = 'any';
    const YEAR_FILTER_LAST_YEAR = 'last_year';
    const YEAR_FILTER_THIS_YEAR = 'this_year';
    const YEAR_FILTER_SELECTED_YEAR = 'selected_date'; 
    const YEAR_FILTER_SELECTED_RANGE = 'selected_range';
    
    //status filter
    const STATUS_FILTER = 'any';
    const STATUS_FILTER_SELECTED = 'selected';
   
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
     * Return data so it is good for CSV export
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array|void
     */
    function runForExport(IUser $user, $additional = null) {
      $data = $this->run($user, $additional);
       
      if($data) {
       
        $begin_csv = array(
        	'Year',
          'Month'
        );
        $currencies = Currencies::getIdDetailsMap();
        foreach ($currencies as $id => $currency) {
          $begin_csv[] = $currency['code'];
        } // foreach
       
        $this->beginExport($begin_csv, array_var($additional, 'export_format'));
        
        foreach($data as $year => $records) {
          if(is_foreachable($records)) {
            foreach ($records as $month => $value) {
                $csv_line = array(
                  $year,
                  $month
                );
                foreach ($value as $curr_id => $amount) {
                  $csv_line[] = $amount;
                }//foreach
                 $this->exportWriteLine($csv_line);
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
        $query = "SELECT * FROM $payments_table WHERE $conditions ORDER BY paid_on";
      } else {
        $query = "SELECT * FROM $payments_table ORDER BY paid_on";
      }//if
      
      $rows = DB::execute($query);
      if($rows) {

        $currencies = Currencies::getIdDetailsMap();
        $months = Globalization::getMonthNames($user);
        $blank_tmp = array();
        
        //create blank array with months and currencies
        foreach ($months as $month_num => $month_name) {
          foreach ($currencies as $id => $currency) {
            $blank_tmp[$month_name][$id] = 0; 
          }//foreach
        }//foreach
        
        $results = array();
        
        foreach ($rows as $k => $payment) {
          $paid_on = new DateValue($payment['paid_on']);
          $year = $paid_on->getYear();
          $month = $paid_on->getMonth();
          $currency_id = $payment['currency_id'];
          $amount = $payment['amount'];
          if(empty($results[$year])) {
            $results[$year] = $blank_tmp;
          }//if
         
          //calculate amount for specific year, ,onth and currency
          $results[$year][$months[$month]][$currency_id] += $amount;
          
        }//foreach
        
        return $results;
       
      } // if
      
    } // runList
    
    
    /**
     * Prepare result conditions based on report settings
     *
     * @param IUser $user
     * @return array
     */
    function prepareConditions(IUser $user) { 
      $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today
      $today_year = $today->getYear();

      $conditions = array();

      switch($this->getDateFilter()) {
        case self::YEAR_FILTER_THIS_YEAR:
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($today_year) . ')';
          break;
        case self::YEAR_FILTER_LAST_YEAR:
          $last_year = $today_year - 1;
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($last_year) . ')';
          break;
        case self::YEAR_FILTER_SELECTED_YEAR:
          $conditions[] = "(YEAR(paid_on) = " . DB::escape($this->getDateFilterSelectedDate()) . ')';
          break;
        // Specific range
        case self::YEAR_FILTER_SELECTED_RANGE:
          list($year_from, $year_to) = $this->getDateFilterSelectedRange();
          
          if($year_from && $year_to) {
            $year_from_str = DB::escape($year_from);
            $year_to_str = DB::escape($year_to);
            
            $conditions[] = "(YEAR(paid_on) BETWEEN $year_from_str AND $year_to_str)";
          } else {
            throw new DataFilterConditionsError('date_filter', $this->getDateFilter(), array($year_from, $year_to), 'Year range not selected');
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
        case self::YEAR_FILTER_SELECTED_YEAR:
          $result['year'] = $this->getDateFilterSelectedDate();
          break;
          
        case self::YEAR_FILTER_SELECTED_RANGE:
          list($year_from, $year_to) = $this->getDateFilterSelectedRange();
          
          $result['year_from'] = $year_from;
          $result['year_to'] = $year_to;
          
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
      return 'payments_summary_report';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('payments_summary_report_id' => $this->getId());
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
          case self::YEAR_FILTER_SELECTED_YEAR:
            $this->filterByDate($attributes['year']);
            break;
          case self::YEAR_FILTER_SELECTED_RANGE:
            $this->filterByRange($attributes['year_from'], $attributes['year_to']);
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
      
      if(isset($attributes['payment_status_filter'])) {
        switch ($attributes['payment_status_filter']) {
          case self::STATUS_FILTER_SELECTED:
            $this->filterByStatus($attributes['payment_status_selected']);
            break;
          default:
            $this->setStatusFilter($attributes['payment_status_filter']);
        }//switch
      }//if
     
      parent::setAttributes($attributes);
    } // setAttributes
  
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
      return $this->getAdditionalProperty('date_filter', self::YEAR_FILTER_ANY);
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
     * Filter objects tracked for a given year
     *
     * @param string $date
     */
    function filterByDate($date) {
      $this->setDateFilter(self::YEAR_FILTER_SELECTED_YEAR);
      $this->setAdditionalProperty('year', (integer) $date);
    } // filterByDate
    
    /**
     * Return selected date for date value
     *
     * @return DateValue
     */
    function getDateFilterSelectedDate() {
      return $this->getAdditionalProperty('year');
    } // getDateFilterSelectedDate
    
    /**
     * Filter records by date range
     *
     * @param string $from
     * @param string $to
     */
    function filterByRange($from, $to) {
      $this->setDateFilter(self::YEAR_FILTER_SELECTED_RANGE);

      $this->setAdditionalProperty('year_from', (integer) $from);
      $this->setAdditionalProperty('year_to', (integer) $to);
    } // filterByRange
    
    /**
     * Return selected range for date filter
     *
     * @return array
     */
    function getDateFilterSelectedRange() {
      return array($this->getAdditionalProperty('year_from'), $this->getAdditionalProperty('year_to'));
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