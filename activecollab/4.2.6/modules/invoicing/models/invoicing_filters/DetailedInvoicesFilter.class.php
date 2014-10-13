<?php

  /**
   * Detailed invoices filter class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class DetailedInvoicesFilter extends InvoicesFilter {

    /**
     * Run the filter
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array
     * @throws InvalidInstanceError
     */
    function run(IUser $user, $additional = null) {
      if($user instanceof User) {
        list($invoices, $projects, $companies) = $this->queryInvoicesData($user);

        if($invoices instanceof DBResult) {
          $invoices = $this->groupInvoices($user, $invoices, $projects, $companies);

          if($invoices) {
            foreach($invoices as $k => $v) {
              if($invoices[$k]['invoices']) {
                foreach($invoices[$k]['invoices'] as $invoice_id => $invoice) {
                  $this->prepareRecordDetails($invoices[$k]['invoices'][$invoice_id], $projects, $companies);
                } // foreach
              } // if

              $this->prepareGroupTotals($invoices[$k]);
            } // foreach
          } // if

          return $invoices;
        } else {
          return null;
        } // if
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
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
      $result = $this->run($user, $additional);

      if($result) {
        $currencies = Currencies::getIdDetailsMap();

        $columns = array(
          'ID',
          'Number',
          'Status',
          'Client',
          'Client ID',
          'Project',
          'Project ID',
          'Issued On',
          'Due On',
          'Closed On',
          'Paid Amount',
          'Balance Due',
          'Total',
          'Currency',
          'Currency Code',
        );

        $this->beginExport($columns, array_var($additional, 'export_format'));

        foreach($result as $v) {
          if($v['invoices']) {
            foreach($v['invoices'] as $invoice) {
              $currency_id = $invoice['currency_id'];

              $this->exportWriteLine(array(
                $invoice['id'],
                $invoice['status'] > 0 ? '#' . $invoice['name'] : $invoice['name'],
                $invoice['status'],
                $invoice['client'] && $invoice['client']['id'] ? $invoice['client']['name'] : null,
                $invoice['client'] && $invoice['client']['id'] ? $invoice['client']['id'] : 0,
                $invoice['project'] && $invoice['project']['id'] ? $invoice['project']['name'] : null,
                $invoice['project'] && $invoice['project']['id'] ? $invoice['project']['id'] : 0,
                $invoice['issued_on'] instanceof DateValue ? $invoice['issued_on']->toMySQL() : null,
                $invoice['due_on'] instanceof DateValue ? $invoice['due_on']->toMySQL() : null,
                $invoice['closed_on'] instanceof DateValue ? $invoice['closed_on']->toMySQL() : null,
                (float) $invoice['paid_amount'],
                (float) $invoice['balance_due'],
                (float) $invoice['total'],
                isset($currencies[$currency_id]) ? $currencies[$currency_id]['name'] : null,
                isset($currencies[$currency_id]) ? $currencies[$currency_id]['code'] : null,
              ));
            } // foreach
          } // if
        } // foreach

        return $this->completeExport();
      } // if

      return null;
    } // runForExport

    /**
     * Cached invoice URL pattern
     *
     * @var bool
     */
    private $invoice_url_pattern = false;

    /**
     * Cached company URL pattern
     *
     * @var string
     */
    private $company_url_pattern = false;

    /**
     * Cached project URL pattern
     *
     * @var string
     */
    private $project_url_pattern = false;

    /**
     * Prepare details of each individual record
     *
     * @param array $invoice
     * @param array $projects
     * @param array $companies
     */
    protected function prepareRecordDetails(&$invoice, $projects, $companies) {
      if($this->invoice_url_pattern === false) {
        $this->invoice_url_pattern = Router::assemble('invoice', array('invoice_id' => '--INVOICE-ID--'));
      } // if

      if($this->company_url_pattern === false) {
        $this->company_url_pattern = Router::assemble('people_company', array('company_id' => '--COMPANY-ID--'));
      } // if

      if($this->project_url_pattern === false) {
        $this->project_url_pattern = Router::assemble('project', array('project_slug' => '--PROJECT-SLUG--'));
      } // if

      $company_id = array_var($invoice, 'company_id', 0, true);
      $project_id = array_var($invoice, 'project_id', 0, true);

      $invoice['name'] = Invoices::getInvoiceName($invoice['id'], $invoice['status'], $invoice['number'], true);
      $invoice['url'] = str_replace('--INVOICE-ID--', $invoice['id'], $this->invoice_url_pattern);

      if($company_id && isset($companies[$company_id])) {
        $invoice['client'] = array(
          'id' => $company_id,
          'name' => $companies[$company_id],
          'url' => str_replace('--COMPANY-ID--', $company_id, $this->company_url_pattern),
        );
      } else {
        $invoice['client'] = array(
          'id' => 0,
          'name' => lang('N/A'),
          'url' => '#',
        );
      } // if

      if($project_id && isset($projects[$project_id])) {
        $invoice['project'] = array(
          'id' => $project_id,
          'name' => $projects[$project_id]['name'],
          'url' => str_replace('--PROJECT-SLUG--', $projects[$project_id]['slug'], $this->project_url_pattern),
        );
      } else {
        $invoice['project'] = array(
          'id' => 0,
          'name' => lang('N/A'),
          'url' => '#',
        );
      } // if
    } // prepareRecordDetails

    /**
     * Prepare group totals
     *
     * @param $group
     */
    function prepareGroupTotals(&$group) {
      $group['total_due'] = array();
      $group['total'] = array();

      if($group['invoices']) {
        foreach($group['invoices'] as $invoice) {
          $currency_id = $invoice['currency_id'];

          if(!isset($group['total_due'][$currency_id])) {
            $group['total_due'][$currency_id] = 0;
          } // if

          if(!isset($group['total'][$currency_id])) {
            $group['total'][$currency_id] = 0;
          } // if

          $group['total_due'][$currency_id] += $invoice['balance_due'];
          $group['total'][$currency_id] += $invoice['total'];
        } // foreach
      } // if
    } // prepareGroupTotals

    /**
     * Go through result entries and make sure that they can be reliable converted to JavaScript maps
     *
     * @param $result
     */
    function resultToMap(&$result) {
      if($result) {
        foreach($result as $k => $v) {
          if($result[$k]['invoices']) {
            $result[$k]['invoices'] = JSON::valueToMap($result[$k]['invoices']); // Convert group invoices to map
          } // if
        } // foreach
      } // if
    } // resultToMap

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'detailed_invoices_filter';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'detailed_invoices_filter_id' => $this->getId(),
      );
    } // getRoutingContextParams

    // ---------------------------------------------------
    //  Query the Data
    // ---------------------------------------------------

    /**
     * Group invoices
     *
     * @param User $user
     * @param DBResult|array $invoices
     * @param array $projects
     * @param array $companies
     * @return array
     */
    protected function groupInvoices(User $user, $invoices, $projects, $companies) {
      switch($this->getGroupBy()) {
        case self::GROUP_BY_STATUS:
          return $this->groupByStatus($user, $invoices);
        case self::GROUP_BY_PROJECT:
          return $this->groupByProject($invoices, $projects);
        case self::GROUP_BY_CLIENT:
          return $this->groupByClient($invoices, $companies);
        case self::GROUP_BY_ISSUED_ON:
          return $this->groupByDateField($invoices, $user, 'issued_on');
        case self::GROUP_BY_DUE_ON:
          return $this->groupByDateField($invoices, $user, 'due_on');
        case self::GROUP_BY_CLOSED_ON:
          return $this->groupByDateField($invoices, $user, 'closed_on');
        default:
          return $this->groupUngrouped($invoices);
      } // switch
    } // groupInvoices

    /**
     * Return invoices grouped by status
     *
     * @param User $user
     * @param DBResult|array $invoices
     * @return array
     */
    private function groupByStatus(User $user, $invoices) {
      $result = array(
        'draft' => array(
          'label' => lang('Draft'),
          'invoices' => array(),
        ),
        'issued' => array(
          'label' => lang('Issued'),
          'invoices' => array(),
        ),
        'overdue' => array(
          'label' => lang('Overdue'),
          'invoices' => array(),
        ),
        'paid' => array(
          'label' => lang('Paid'),
          'invoices' => array(),
        ),
        'canceled' => array(
          'label' => lang('Canceled'),
          'invoices' => array(),
        ),
      );

      $reference = DateTimeValue::now()->advance(get_user_gmt_offset($user), false);
      $reference = DateValue::make($reference->getMonth(),$reference->getDay(), $reference->getYear());

      foreach($invoices as $invoice) {
        switch($invoice['status']) {
          case INVOICE_STATUS_DRAFT:
            $result['draft']['invoices'][$invoice['id']] = $invoice;
            break;
          case INVOICE_STATUS_ISSUED:
            if($invoice['due_on'] instanceof DateValue && $invoice['due_on']->getTimestamp() < $reference->getTimestamp()) {
              $result['overdue']['invoices'][$invoice['id']] = $invoice;
            } else {
              $result['issued']['invoices'][$invoice['id']] = $invoice;
            } // if

            break;
          case INVOICE_STATUS_PAID:
            $result['paid']['invoices'][$invoice['id']] = $invoice;
            break;
          case INVOICE_STATUS_CANCELED:
            $result['canceled']['invoices'][$invoice['id']] = $invoice;

            break;
        } // switch

        $project_id = $invoice['project_id'];

        if(isset($result["project-$project_id"])) {
          $result["project-$project_id"]['invoices'][$invoice['id']] = $invoice;
        } else {
          $result['unknow-project']['invoices'][$invoice['id']] = $invoice;
        } // if
      } // foreach

      return $result;
    } // groupByStatus

    /**
     * Return invoices grouped by project
     *
     * @param DBResult|array $invoices
     * @param array $projects
     * @return array
     */
    private function groupByProject($invoices, $projects) {
      $result = array();

      if($projects) {
        foreach($projects as $k => $v) {
          $result["project-$k"] = array(
            'label' => $v['name'],
            'invoices' => array(),
          );
        } // foreach
      } // if

      $result['unknow-project'] = array(
        'label' => lang('Unknown'),
        'invoices' => array(),
      );

      foreach($invoices as $invoice) {
        $project_id = $invoice['project_id'];

        if(isset($result["project-$project_id"])) {
          $result["project-$project_id"]['invoices'][$invoice['id']] = $invoice;
        } else {
          $result['unknow-project']['invoices'][$invoice['id']] = $invoice;
        } // if
      } // foreach

      return $result;
    } // groupByProject

    /**
     * Return invoices grouped by client company
     *
     * @param array $invoices
     * @param array $companies
     * @return array
     */
    private function groupByClient($invoices, $companies) {
      $result = array();

      if($companies) {
        foreach($companies as $k => $v) {
          $result["company-$k"] = array(
            'label' => $v,
            'invoices' => array(),
          );
        } // foreach
      } // if

      $result['unknow-company'] = array(
        'label' => lang('Unknown'),
        'invoices' => array(),
      );

      foreach($invoices as $invoice) {
        $company_id = $invoice['company_id'];

        if(isset($result["company-$company_id"])) {
          $result["company-$company_id"]['invoices'][$invoice['id']] = $invoice;
        } else {
          $result['unknow-company']['invoices'][$invoice['id']] = $invoice;
        } // if
      } // foreach

      return $result;
    } // groupByClient

    /**
     * Return invoices grouped by client company
     *
     * @param array $invoices
     * @param User $user
     * @param string $date_field
     * @return array
     */
    private function groupByDateField($invoices, $user, $date_field) {
      $result = array();

      if($date_field == 'due_on' || $date_field == 'issued_on') {
        $not_set_label = lang('Draft Invoices');
      } else {
        $not_set_label = lang('Not Paid Yet, or Canceled');
      } // if

      $date_not_set = array(
        'label' => $not_set_label,
        'invoices' => array(),
      );

      foreach($invoices as $invoice) {
        $date = $invoice[$date_field];

        if($date instanceof DateValue) {
          $key = 'date-' . $date->toMySQL();

          if(!isset($result[$key])) {
            $result[$key] = array(
              'label' => $date->formatDateForUser($user, 0),
              'invoices' => array(),
            );
          } // if

          $result[$key]['invoices'][$invoice['id']] = $invoice;
        } else {
          $date_not_set['invoices'][$invoice['id']] = $invoice;
        } // if
      } // foreach

      $result['date-not-set'] = $date_not_set;

      return $result;
    } // groupByDateField

    /**
     * Return invoices grouped in All group (ungrouped)
     *
     * @param array $invoices
     * @return array
     */
    private function groupUngrouped($invoices) {
      $result['all'] = array(
        'label' => lang('All Invoices'),
        'invoices' => array(),
      );

      foreach($invoices as $invoice) {
        $result['all']['invoices'][$invoice['id']] = $invoice;
      } // foreach

      return $result;
    } // groupUngrouped

    /**
     * Query invoices data
     *
     * @param User $user
     * @return array
     */
    protected function queryInvoicesData(User $user) {
      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';

      try {
        $conditions = $this->prepareConditions($user);
      } catch(DataFilterConditionsError $e) {
        $conditions = null;
      } // try

      $companies = $projects = array();

      if($conditions) {
        $invoices = DB::execute("SELECT id, company_id, company_name, project_id, currency_id, varchar_field_1 AS 'number', subtotal, tax, total, balance_due, paid_amount, status, created_on, date_field_2 AS 'issued_on', date_field_1 AS 'due_on', closed_on FROM $invoice_objects_table WHERE $conditions ORDER BY due_on DESC");
        if($invoices instanceof DBResult) {
          $invoices->setCasting(array(
            'id' => DBResult::CAST_INT,
            'company_id' => DBResult::CAST_INT,
            'project_id' => DBResult::CAST_INT,
            'currency_id' => DBResult::CAST_INT,
            'subtotal' => DBResult::CAST_FLOAT,
            'tax' => DBResult::CAST_FLOAT,
            'total' => DBResult::CAST_FLOAT,
            'balance_due' => DBResult::CAST_FLOAT,
            'paid_amount' => DBResult::CAST_FLOAT,
            'status' => DBResult::CAST_INT,
            'created_on' => DBResult::CAST_DATETIME,
            'issued_on' => DBResult::CAST_DATETIME,
            'due_on' => DBResult::CAST_DATE,
            'closed_on' => DBResult::CAST_DATETIME,
          ));

          foreach($invoices as $invoice) {
            $company_id = $invoice['company_id'];
            $project_id = $invoice['project_id'];

            if($company_id && !isset($companies[$company_id])) {
              $companies[$company_id] = null;
            } // if

            if($project_id && !isset($projects[$project_id])) {
              $projects[$project_id] = null;
            } // if
          } // foreach

          $companies = count($companies) ? Companies::getIdNameMap(array_keys($companies)) : null;
          $projects = count($projects) ? Projects::getIdDetailsMap(array('name', 'slug'), array_keys($projects)) : null;
        } // if
      } else {
        $invoices = null;
      } // if

      return array($invoices, $projects, $companies);
    } // queryInvoicesData

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