<?php

  /**
   * Invoices filters
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  abstract class InvoicesFilter extends DataFilter {

    // Status filter
    const STATUS_FILTER_ANY = 'any';
    const STATUS_FILTER_DRAFT = 'draft';
    const STATUS_FILTER_ISSUED = 'issued';
    const STATUS_FILTER_OVERDUE = 'overdue';
    const STATUS_FILTER_PAID = 'paid';
    const STATUS_FILTER_CANCELED = 'canceled';
    const STATUS_FILTER_ALL_EXCEPT_CANCELED = 'all_except_canceled';

    // Group
    const DONT_GROUP = 'dont';
    const GROUP_BY_STATUS = 'status';
    const GROUP_BY_PROJECT = 'project';
    const GROUP_BY_CLIENT = 'client';
    const GROUP_BY_ISSUED_ON = 'issued_on';
    const GROUP_BY_DUE_ON = 'due_on';
    const GROUP_BY_CLOSED_ON = 'closed_on';

    const CLIENT_FILTER_ANY = 'any';
    const CLIENT_FILTER_SELECTED = 'selected';


    /**
     * Prepare filter conditions
     *
     * @param User $user
     * @return string
     */
    protected function prepareConditions(User $user) {
      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';

      $conditions = array("($invoice_objects_table.type = 'Invoice' AND $invoice_objects_table.state > 1)");

      if($this->getProjectFilter() != Projects::PROJECT_FILTER_ANY) {
        $conditions[] = DB::prepare("($invoice_objects_table.project_id IN (?))", Projects::getProjectIdsByDataFilter($this, $user)); // Get projects that we can query based on given criteria
      } // if

      // Status filter
      switch($this->getStatusFilter()) {
        case self::STATUS_FILTER_ISSUED:
          $conditions[] = DB::prepare("($invoice_objects_table.status = ?)", INVOICE_STATUS_ISSUED);
          break;
        case self::STATUS_FILTER_OVERDUE:
          $conditions[] = DB::prepare("($invoice_objects_table.status = ? AND $invoice_objects_table.date_field_1 < DATE(?))", INVOICE_STATUS_ISSUED, DateTimeValue::now()->advance(get_user_gmt_offset($user), false));
          break;
        case self::STATUS_FILTER_PAID:
          $conditions[] = DB::prepare("($invoice_objects_table.status = ?)", INVOICE_STATUS_PAID);
          break;
        case self::STATUS_FILTER_CANCELED:
          $conditions[] = DB::prepare("($invoice_objects_table.status = ?)", INVOICE_STATUS_CANCELED);
          break;
        case self::STATUS_FILTER_DRAFT:
          $conditions[] = DB::prepare("($invoice_objects_table.status = ?)", INVOICE_STATUS_DRAFT);
          break;
        case self::STATUS_FILTER_ALL_EXCEPT_CANCELED:
          $conditions[] = DB::prepare("($invoice_objects_table.status IN (?))", array(INVOICE_STATUS_DRAFT, INVOICE_STATUS_ISSUED, INVOICE_STATUS_PAID));
          break;
      } // switch

      if(!$this->getIncludeCreditInvoices()) {
        $conditions[] = DB::prepare("($invoice_objects_table.total > ?)", array(0));
      } //if


      if($this->getClientFilter() != self::CLIENT_FILTER_ANY) {
        $conditions[] = DB::prepare("($invoice_objects_table.company_id = ?)", $this->getClientId());
      } //if

      $this->prepareUserFilterConditions($user, 'issued', $invoice_objects_table, $conditions, 'integer_field_1');
      $this->prepareDateFilterConditions($user, 'issued', $invoice_objects_table, $conditions, 'date_field_2');
      $this->prepareDateFilterConditions($user, 'due', $invoice_objects_table, $conditions, 'date_field_1');
      $this->prepareDateFilterConditions($user, 'closed', $invoice_objects_table, $conditions);

      return implode(' AND ', $conditions);
    } // prepareConditions

    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['status_filter'])) {
        $this->setStatusFilter($attributes['status_filter']);
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

      if(isset($attributes['include_credit_invoices'])) {
        $this->setIncludeCreditInvoices($attributes['include_credit_invoices']);
      }//if

      if($attributes['client_filter'] == self::CLIENT_FILTER_SELECTED) {
        $this->filterByClientId(array_var($attributes, 'client_id'));
      } //if

      $this->setUserFilterAttributes('issued', $attributes);
      $this->setDateFilterAttributes('issued', $attributes);
      $this->setDateFilterAttributes('due', $attributes);
      $this->setDateFilterAttributes('closed', $attributes);

      if(isset($attributes['group_by'])) {
        $this->setGroupBy($attributes['group_by']);
      } // if

      parent::setAttributes($attributes);
    } // setAttributes

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

      $result['status_filter'] = $this->getStatusFilter();
      $result['group_by'] = $this->getGroupBy();
      $result['include_credit_invoices'] = $this->getIncludeCreditInvoices();

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

      $result['client_filter'] = $this->getClientFilter();
      $result['client_id'] = $this->getClientId();

      $this->describeUserFilter('issued', $result);
      $this->describeDateFilter('issued', $result);
      $this->describeDateFilter('due', $result);
      $this->describeDateFilter('closed', $result);

      return $result;
    } // describe


    /**
     * Get include credit invoices
     *
     */
    function getIncludeCreditInvoices() {
      return $this->getAdditionalProperty('include_credit_invoices');
    }//getIncludeCreditInvoices

    /**
     * Set include credit invoices
     *
     */
    function setIncludeCreditInvoices($value) {
      return $this->setAdditionalProperty('include_credit_invoices', $value);
    }//setIncludeCreditInvoices


    /**
     * Return user filter value
     *
     * @return string
     */
    function getStatusFilter() {
      return $this->getAdditionalProperty('status_filter', self::STATUS_FILTER_ANY);
    } // getStatusFilter

    /**
     * Set user filter value
     *
     * @param string $value
     * @return string
     */
    function setStatusFilter($value) {
      return $this->setAdditionalProperty('status_filter', $value);
    } // setStatusFilter

    /**
     * Return client filter
     *
     * @return string
     */
    function getClientFilter() {
      return $this->getAdditionalProperty('client_filter', self::CLIENT_FILTER_ANY);
    } // getClientFilter

    /**
     * Set client filter value
     *
     * @param string $value
     * @return string
     */
    function setClientFilter($value) {
      return $this->setAdditionalProperty('client_filter', $value);
    } // setClientFilter

    /**
     * Return client ID
     *
     * @return integer
     */
    function getClientId() {
      return (integer) $this->getAdditionalProperty('client_id');
    } // getClientId

    /**
     * Set filter to filter records by $client_id
     *
     * @param integer $client_id
     * @return integer
     */
    function filterByClientId($client_id) {
      $this->setClientFilter(self::CLIENT_FILTER_SELECTED);
      $this->setAdditionalProperty('client_id', (integer) $client_id);
    } // filterByClientId

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
      if($project_client_id instanceof Company) {
        $this->setAdditionalProperty('project_client_id', $project_client_id->getId());
      } else {
        $this->setAdditionalProperty('project_client_id', (integer) $project_client_id);
      } // if
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
     * Return user filter value
     *
     * @return string
     */
    function getIssuedByFilter() {
      return $this->getAdditionalProperty('issued_by_filter', DataFilter::USER_FILTER_ANYBODY);
    } // getIssuedByFilter

    /**
     * Set user filter value
     *
     * @param string $value
     * @return string
     */
    function setIssuedByFilter($value) {
      return $this->setAdditionalProperty('issued_by_filter', $value);
    } // setIssuedByFilter

    /**
     * Set filter by company values
     *
     * @param integer $company_id
     */
    function issuedByCompanyMember($company_id) {
      $this->setIssuedByFilter(DataFilter::USER_FILTER_COMPANY_MEMBER);
      $this->setAdditionalProperty('issued_by_company_member_id', $company_id);
    } // issuedByCompanyMember

    /**
     * Return company ID set for user filter
     *
     * @return integer
     */
    function getIssuedByCompanyMember() {
      return $this->getAdditionalProperty('issued_by_company_member_id');
    } // getIssuedByCompanyMember

    /**
     * Set user filter to filter only tracked object for selected users
     *
     * @param array $users
     */
    function issuedByUsers($users) {
      $this->setIssuedByFilter(DataFilter::USER_FILTER_SELECTED);

      if(is_array($users)) {
        $user_ids = array();

        foreach($users as $k => $v) {
          $user_ids[$k] = $v instanceof User ? $v->getId() : (integer) $v;
        } // foreach
      } else {
        $user_ids = null;
      } // if

      $this->setAdditionalProperty('issued_by_selected_users', $user_ids);
    } // issuedByUsers

    /**
     * Return array of selected users
     *
     * @return array
     */
    function getIssuedByUsers() {
      return $this->getAdditionalProperty('issued_by_selected_users');
    } // getIssuedByUsers

    /**
     * Return issued date filter value
     *
     * @return string
     */
    function getIssuedOnFilter() {
      return $this->getAdditionalProperty('issued_on_filter', DataFilter::DATE_FILTER_ANY);
    } // getIssuedOnFilter

    /**
     * Set issued date filter value
     *
     * @param string $value
     * @return string
     */
    function setIssuedOnFilter($value) {
      return $this->setAdditionalProperty('issued_on_filter', $value);
    } // setIssuedOnFilter

    /**
     * Filter assignents that are issued on a given date
     *
     * @param string $date
     */
    function issuedOnDate($date) {
      $this->setIssuedOnFilter(DataFilter::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('issued_on_filter_on', (string) $date);
    } // issuedOnDate

    /**
     * Return issued on filter value
     *
     * @return DateValue
     */
    function getIssuedOnDate() {
      $on = $this->getAdditionalProperty('issued_on_filter_on');

      return $on ? new DateValue($on) : null;
    } // getIssuedOnDate

    /**
     * Return invoices that are issued in a given range
     *
     * @param string $from
     * @param string $to
     */
    function issuedInRange($from, $to) {
      $this->setIssuedOnFilter(DataFilter::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('issued_on_filter_from', (string) $from);
      $this->setAdditionalProperty('issued_on_filter_to', (string) $to);
    } // issuedInRange

    /**
     * Return issued on filter range
     *
     * @return array
     */
    function getIssuedInRange() {
      $from = $this->getAdditionalProperty('issued_on_filter_from');
      $to = $this->getAdditionalProperty('issued_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getIssuedInRange

    /**
     * Return due date filter value
     *
     * @return string
     */
    function getDueOnFilter() {
      return $this->getAdditionalProperty('due_on_filter', DataFilter::DATE_FILTER_ANY);
    } // getDueOnFilter

    /**
     * Set due date filter value
     *
     * @param string $value
     * @return string
     */
    function setDueOnFilter($value) {
      return $this->setAdditionalProperty('due_on_filter', $value);
    } // setDueOnFilter

    /**
     * Filter invoices that are due on a given date
     *
     * @param string $date
     */
    function dueOnDate($date) {
      $this->setDueOnFilter(DataFilter::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('due_on_filter_on', (string) $date);
    } // dueOnDate

    /**
     * Return due on filter value
     *
     * @return DateValue
     */
    function getDueOnDate() {
      $on = $this->getAdditionalProperty('due_on_filter_on');

      return $on ? new DateValue($on) : null;
    } // getDueOnDate

    /**
     * Return invoices that are due in a given range
     *
     * @param string $from
     * @param string $to
     */
    function dueInRange($from, $to) {
      $this->setDueOnFilter(DataFilter::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('due_on_filter_from', (string) $from);
      $this->setAdditionalProperty('due_on_filter_to', (string) $to);
    } // dueInRange

    /**
     * Return due on filter range
     *
     * @return array
     */
    function getDueInRange() {
      $from = $this->getAdditionalProperty('due_on_filter_from');
      $to = $this->getAdditionalProperty('due_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getDueInRange

    /**
     * Return closed date filter value
     *
     * @return string
     */
    function getClosedOnFilter() {
      return $this->getAdditionalProperty('closed_on_filter', DataFilter::DATE_FILTER_ANY);
    } // getClosedOnFilter

    /**
     * Set closed date filter value
     *
     * @param string $value
     * @return string
     */
    function setClosedOnFilter($value) {
      return $this->setAdditionalProperty('closed_on_filter', $value);
    } // setClosedOnFilter

    /**
     * Filter invoices that are closed on a given date
     *
     * @param string $date
     */
    function closedOnDate($date) {
      $this->setClosedOnFilter(DataFilter::DATE_FILTER_SELECTED_DATE);
      $this->setAdditionalProperty('closed_on_filter_on', (string) $date);
    } // closedOnDate

    /**
     * Return closed on filter value
     *
     * @return DateValue
     */
    function getClosedOnDate() {
      $on = $this->getAdditionalProperty('closed_on_filter_on');

      return $on ? new DateValue($on) : null;
    } // getClosedOnDate

    /**
     * Return invoices that are closed in a given range
     *
     * @param string $from
     * @param string $to
     */
    function closedInRange($from, $to) {
      $this->setClosedOnFilter(DataFilter::DATE_FILTER_SELECTED_RANGE);
      $this->setAdditionalProperty('closed_on_filter_from', (string) $from);
      $this->setAdditionalProperty('closed_on_filter_to', (string) $to);
    } // closedInRange

    /**
     * Return closed on filter range
     *
     * @return array
     */
    function getClosedInRange() {
      $from = $this->getAdditionalProperty('closed_on_filter_from');
      $to = $this->getAdditionalProperty('closed_on_filter_to');

      return $from && $to ? array(new DateValue($from), new DateValue($to)) : array(null, null);
    } // getClosedInRange

    /**
     * Use by managers for serious reporting, so it needs to go through all projects
     *
     * @return bool
     */
    function getIncludeAllProjects() {
      return true;
    } // getIncludeAllProjects

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
      return $this->setAdditionalProperty('group_by', $value);
    } // setGroupBy

  }