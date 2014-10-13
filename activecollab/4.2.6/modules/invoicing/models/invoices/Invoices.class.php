<?php

  /**
   * Invoices manager class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Invoices extends InvoiceObjects {

    /**
     * Return invoice hash
     *
     * @param $length
     * @return string
     */
    static function generateHash($length = 20) {
      $string = microtime();
      return substr(sha1($string), 0, $length);
    } //generateHash

    /**
     * Returns true if $user can access $company invoices
     *
     * @param User $user
     * @param Company $company
     * @return boolean
     */
    static function canAccessCompanyInvoices($user, Company $company) {
      if($user->isFinancialManager()) {
        return true;
      } // if

      return Invoices::canManageClientCompanyFinances($company, $user);
    } // canAccessCompanyInvoices

    /**
     * Return true is system should notify client when invoice canceled
     *
     * @return Boolean
     */
    static function getNotifyClientAboutCanceledInvoice() {
      return boolval(ConfigOptions::getValue('invoice_notify_on_cancel'));
    }//getNotifyClientAboutCanceledInvoice

    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------

    /**
     * Return invoice name based on the set of given parameters
     *
     * This method was extracted so we can use it in reports, and other application areas without
     * creating a new invoice instance in order to get its properly formatted name
     *
     * @param int $id
     * @param int $status
     * @param string $number
     * @param bool $short
     * @return string
     */
    static function getInvoiceName($id, $status, $number, $short = false) {
      if($status == INVOICE_STATUS_DRAFT) {
        if($number) {
          return $short ? $number : lang(':invoice_show_as #:invoice_num', array(
            'invoice_show_as' => Invoices::printProformaInvoiceAs(),
            'invoice_num' => $number
          ));
        } else {
          return $short ? $number : lang(':invoice_show_as #:invoice_num', array(
            'invoice_show_as' => Invoices::printProformaInvoiceAs(),
            'invoice_num' => $id
          ));

//          return lang('Draft #:invoice_num', array(
//            'invoice_num' => $id
//          ));
        } // if
      } else {
        return $short ? $number : lang(':invoice_show_as #:invoice_num', array(
          'invoice_show_as' => Invoices::printInvoiceAs(),
          'invoice_num' => $number
        ));
      } // if
    } // getInvoiceName

    /**
     * Return PDF file name
     *
     * @param Invoice $invoice
     * @return string
     */
    static function getInvoicePdfName(Invoice $invoice) {
      if($invoice->getStatus() == INVOICE_STATUS_DRAFT) {
        $print_as = Inflector::transliterate(Invoices::printProformaInvoiceAs());

        if(empty($print_as)) {
          $print_as = 'Draft';
        } // if
      } else {
        $print_as = Inflector::transliterate(Invoices::printInvoiceAs());

        if(empty($print_as)) {
          $print_as = 'Invoice';
        } // if
      } // if

      $number= $invoice->getNumber();
      if(empty($number)) {
        $number = $invoice->getId();
      } // if

      return "{$print_as} #{$number}.pdf";
    } // getInvoicePdfName

    /**
     * Cached print invoice as value
     *
     * @var string
     */
    static private $print_invoice_as = null;

    /**
     * Return label used for invoices
     *
     * @return string
     */
    static function printInvoiceAs() {
      if(self::$print_invoice_as === null) {
        self::$print_invoice_as = trim(ConfigOptions::getValue('print_invoices_as'));

        if(empty(self::$print_invoice_as)) {
          self::$print_invoice_as = lang('Invoice');
        } // if
      } // if

      return self::$print_invoice_as;
    } // printInvoiceAs

    /**
     * Cached print invoice as value
     *
     * @var string
     */
    static private $print_proforma_invoice_as = null;

    /**
     * Return label used for proforma invoices
     *
     * @return string
     */
    static function printProformaInvoiceAs() {
      if(self::$print_proforma_invoice_as === null) {
        self::$print_proforma_invoice_as = trim(ConfigOptions::getValue('print_proforma_invoices_as'));

        if(empty(self::$print_proforma_invoice_as)) {
          self::$print_proforma_invoice_as = lang('Draft');
        } // if
      } // if

      return self::$print_proforma_invoice_as;
    } // printProformaInvoiceAs

    // ---------------------------------------------------
    //  Item formatters
    // ---------------------------------------------------

    // Default format values
    const DEFAULT_TASK_DESCRIPTION_FORMAT = 'Task #:task_id: :task_summary (:project_name)';
    const DEFAULT_PROJECT_DESCRIPTION_FORMAT = 'Project :name';
    const DEFAULT_JOB_TYPE_DESCRIPTION_FORMAT = ':job_type';
    const DEFAULT_INDIVIDUAL_DESCRIPTION_FORMAT = ':parent_task_or_project:record_summary (:record_date)';

    /**
     * Generate task line description
     *
     * @param array $variables
     * @return string
     */
    static function generateTaskDescription($variables) {
      return Invoices::generateDescription('description_format_grouped_by_task', Invoices::DEFAULT_TASK_DESCRIPTION_FORMAT, $variables);
    } // generateTaskDescription

    /**
     * Generate project line description
     *
     * @param array $variables
     * @return string
     */
    static function generateProjectDescription($variables) {
      return Invoices::generateDescription('description_format_grouped_by_project', Invoices::DEFAULT_PROJECT_DESCRIPTION_FORMAT, $variables);
    } // generateProjectDescription

    /**
     * Generate description when tracked data is grouped by job type
     *
     * @param array $variables
     * @return string
     */
    static function generateJobTypeDescription($variables) {
      return Invoices::generateDescription('description_format_grouped_by_job_type', Invoices::DEFAULT_JOB_TYPE_DESCRIPTION_FORMAT, $variables);
    } // generateJobTypeDescription

    // Record summary transformation
    const SUMMARY_PUT_IN_PARENTHESES = 'put_in_parentheses';
    const SUMMARY_PREFIX_WITH_DASH = 'prefix_with_dash';
    const SUMMARY_SUFIX_WITH_DASH = 'sufix_with_dash';
    const SUMMARY_PREFIX_WITH_COLON = 'prefix_with_colon';
    const SUMMARY_SUFIX_WITH_COLON = 'sufix_with_colon';

    /**
     * Generate individual item description
     *
     * @param array $variables
     * @return string
     */
    static function generateIndividualDescription($variables) {
      $summary = trim(array_var($variables, 'record_summary'));

      if($summary) {
        $transformations = array(ConfigOptions::getValue('first_record_summary_transformation'), ConfigOptions::getValue('second_record_summary_transformation'));

        foreach($transformations as $transformation) {
          if($transformation) {
            switch($transformation) {
              case Invoices::SUMMARY_PUT_IN_PARENTHESES:
                $summary = "($summary)";
                break;
              case Invoices::SUMMARY_PREFIX_WITH_DASH:
                $summary = " - $summary";
                break;
              case Invoices::SUMMARY_SUFIX_WITH_DASH:
                $summary = "$summary - ";
                break;
              case Invoices::SUMMARY_PREFIX_WITH_COLON:
                $summary = ": $summary";
                break;
              case Invoices::SUMMARY_SUFIX_WITH_COLON:
                $summary = "$summary: ";
                break;
            } // switch
          } // if
        } // foreach
      } // if

      $variables['record_summary'] = $summary;

      return Invoices::generateDescription('description_format_separate_items', Invoices::DEFAULT_INDIVIDUAL_DESCRIPTION_FORMAT, $variables);
    } // generateIndividualDescription

    /**
     * Generate description based on pattern and variables
     *
     * @param string $pattern_config_option
     * @param string $default_pattern
     * @param array $variables
     * @return mixed
     */
    static private function generateDescription($pattern_config_option, $default_pattern, $variables) {
      $pattern = ConfigOptions::getValue($pattern_config_option);
      if(empty($pattern)) {
        $pattern = $default_pattern;
      } // if

      $replacements = array();

      foreach($variables as $k => $v) {
        $replacements[":$k"] = $v;
      } // foreach

      return str_replace(array_keys($replacements), array_values($replacements), $pattern);
    } // generateDescription

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------

    /**
     * Find invoices by ID-s
     *
     * @param array $ids
     * @return DBResult
     */
    static function findByIds($ids) {
      return Invoices::find(array(
        'conditions' => array('id IN (?) AND type = ?', $ids, 'Invoice'),
      ));
    } // findByIds

    /**
     * Find invoice by hash
     *
     * @param string $hash
     * @return Invoice
     */
    static function findByHash($hash) {
      return Invoices::find(array(
        'conditions' => array('hash = ?', $hash),
        'one' => true
      ));
    } // findByHash

    /**
     * Return number of draft invoices
     *
     * @return integer
     */
    static function countDrafts() {
      return Invoices::count(array('status = ? AND type = ?', INVOICE_STATUS_DRAFT, 'Invoice'));
    } // countDrafts

    /**
     * Count overdue invoices (if company is provided, then it counts for that specified company)
     *
     * @param Company $company
     * @return integer
     */
    static function countOverdue($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company instanceof Company) {
        return Invoices::count(array('status = ? AND date_field_1 < ? AND company_id = ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), $company->getId(), 'Invoice'));
      } else {
        return Invoices::count(array('status = ? AND date_field_1 < ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), 'Invoice'));
      } // if
    } // countOverdue

    /**
     * Find overdue invoices (if company is provided, only invoices for that companies are returned)
     *
     * @param Copmany $company
     * @return integer
     */
    static function findOverdue($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company instanceof Company) {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 < ? AND company_id = ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), $company->getId(), 'Invoice'),
          'order' => 'date_field_1 DESC',
        ));
      } else {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 < ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), 'Invoice'),
          'order' => 'date_field_1 DESC',
        ));
      } // if
    } // findOverdue

    /**
     * Count outstanding (overdue invoices are excluded. If company is provided outstanding invoices for that company are counted)
     *
     * @param Company $company
     * @return integer
     */
    static function countOutstanding($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company instanceof Company) {
        return Invoices::count(array('status = ? AND date_field_1 >= ? AND company_id = ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), $company->getId(), 'Invoice'));
      } else {
        return Invoices::count(array('status = ? AND date_field_1 >= ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), 'Invoice'));
      } // if
    } // countOutstanding

    /**
     * Return outstanding invoices (overdue invoices are excluded. If company is provided outstanding invoices for that company are counted)
     *
     * @param Company $company
     * @return array;
     */
    static function findOutstanding($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company instanceof Company) {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 >= ? AND company_id = ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), $company->getId(), 'Invoice'),
          'order' => 'date_field_1 DESC',
        ));
      } else {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 >= ? AND type = ?', INVOICE_STATUS_ISSUED, $today->toMySQL(), 'Invoice'),
          'order' => 'date_field_1 DESC',
        ));
      } // if
    } // findOutstanding

    /**
     * Return invoices by company
     *
     * @param Company $company
     * @param User $user
     * @param mixed $statuses
     * @param string $order_by
     * @return Invoice[]
     */
    static function findByCompany(Company &$company, User $user, $statuses = null, $order_by = 'created_on') {
      if (is_null($statuses)) {
        $statuses = self::getVisibleStatuses($user);
      } // if

      return Invoices::find(array(
        'conditions' => array('company_id = ? AND status IN (?) AND type = ?', $company->getId(), $statuses, 'Invoice'),
        'order' => $order_by,
      ));
    } // findByCompany

    /**
     * Count invoices by company
     *
     * @param Company $company
     * @param User $user
     * @param array $statuses
     * @return integer
     */
    function countByCompany(&$company, User $user, $statuses = null) {
      if (is_null($statuses)) {
        $statuses = self::getVisibleStatuses($user);
      } // if

      return Invoices::count(array('company_id = ? AND status IN (?) AND type = ?', $company->getId(), $statuses, 'Invoice'));
    } // countByCompany

    /**
     * Return ID-s by company
     *
     * @param Company $company
     * @param User $user
     * @return array
     */
    static function findIdsByCompany(Company $company, User $user) {
      return DB::executeFirstRow('SELECT id FROM ' . TABLE_PREFIX . 'invoice_objects WHERE company_id = ? AND status IN (?) AND type = ?', $company->getId(), self::getVisibleStatuses($user), 'Invoice');
    } // findIdsByCompany

    /**
     * Return summarized company invoices information
     *
     * @param User $user
     * @param mixed $statuses
     * @return array
     */
    function findInvoicedCompaniesInfo(User $user, $statuses = null) {
      $companies_table = TABLE_PREFIX . 'companies';
      $invoices_table = TABLE_PREFIX . 'invoice_objects';

      if (is_null($statuses)) {
        $statuses = self::getVisibleStatuses($user);
      } // if

      return DB::execute("SELECT $companies_table.id, $companies_table.name, COUNT($invoices_table.id) AS 'invoices_count' FROM $companies_table, $invoices_table WHERE $invoices_table.company_id = $companies_table.id AND $invoices_table.status IN (?) AND $invoices_table.type = ? GROUP BY $invoices_table.company_id ORDER BY $companies_table.name ", $statuses, 'Invoice');
    } // findInvoicedCompaniesInfo

    /**
     * Return number of invoices that use $currency
     *
     * @param Currency $currency
     * @return integer
     */
    static function countByCurrency($currency) {
      return Invoices::count(array('currency_id = ? AND type = ?', $currency->getId(), 'Invoice'));
    } // countByCurrency

    /**
     * Increment invoice counters
     *
     * @param integer $year
     * @param integer $month
     * @return boolean
     */
    static function incrementDateInvoiceCounters($year = null, $month = null) {
      if ($year === null) {
        $year = date('Y');
      } // if

      if ($month === null) {
        $month = date('n');
      } // if

      $counters = ConfigOptions::getValue('invoicing_number_date_counters');

      $previous_month_counter = array_var($counters, $year.'_'.$month, 0);
      $previous_year_counter =  array_var($counters, $year, 0);
      $previous_total_counter = array_var($counters, 'total', 0);

      $counters[$year.'_'.$month] = ($previous_month_counter + 1);
      $counters[$year] = ($previous_year_counter + 1);
      $counters['total'] = ($previous_total_counter + 1);
      return ConfigOptions::setValue('invoicing_number_date_counters', $counters);
    } // incrementDateInvoiceCounters

    /**
     * Retrieves invoice counters
     *
     * @param integer $year
     * @param integer $month
     * @return array
     */
    static function getDateInvoiceCounters($year = null, $month = null) {
      if ($year === null) {
        $year = date('Y');
      } // if

      if ($month === null) {
        $month = date('n');
      } // if

      $counters = ConfigOptions::getValue('invoicing_number_date_counters');

      $previous_month_counter = array_var($counters, $year.'_'.$month, 0);
      $previous_year_counter =  array_var($counters, $year, 0);
      $previous_total_counter = array_var($counters, 'total', 0);

      return array($previous_total_counter, $previous_year_counter, $previous_month_counter);
    } // getDateInvoiceCounters

    /**
     * Sets invoice counters
     *
     * @param integer $total_counter
     * @param integer $year_counter
     * @param integer $month_counter
     * @return array
     */
    static function setDateInvoiceCounters($total_counter = null, $year_counter = null, $month_counter = null) {
      $year = date('Y');
      $month = date('n');

      $counters = self::getDateInvoiceCounters();

      if($total_counter) {
        $counters['total'] = $total_counter;
      } // if

      if($month_counter) {
        $counters["{$year}_{$month}"] = $month_counter;
      } // if

      if($year_counter) {
        $counters[$year] = $year_counter;
      } // if

      return ConfigOptions::setValue('invoicing_number_date_counters', $counters);
    } // setDateInvoiceCounters

    /**
     * Get array of statuses
     *
     * @return array
     */
    static function getStatusMap() {
      return array(
        INVOICE_STATUS_DRAFT => lang('Draft'),
        INVOICE_STATUS_ISSUED => lang('Issued'),
        INVOICE_STATUS_PAID => lang('Paid'),
        INVOICE_STATUS_CANCELED => lang('Canceled')
      );
    } // getStatusMap

    /**
     * Get array of statuses that $user can see
     *
     * @param User $user
     * @return array
     */
    function getVisibleStatuses(User $user) {
      $statuses = array(INVOICE_STATUS_CANCELED, INVOICE_STATUS_ISSUED, INVOICE_STATUS_PAID);
      if ($user->isFinancialManager()) {
        $statuses[] = INVOICE_STATUS_DRAFT;
      } // if

      return $statuses;
    } // getVisibleStatuses

    /**
     * Make the map of unique month-year => verbose pairs for given invoices
     *
     * @param array $invoices
     * @return array
     */
    function getIssuedAndDueDatesMap(&$invoices) {
      $map = array(
        'issued_on' => array(),
        'due_on' => array(),
      );

      if (is_foreachable($invoices)) {
        foreach ($invoices as &$invoice) {
          if ($invoice['issued_on_month'] && !isset($map['issued_on'][$invoice['issued_on_month']])) {
            $map['issued_on'][$invoice['issued_on_month']] = date("F Y", strtotime($invoice['issued_on_month'] . '-1'));
          } // if

          if ($invoice['due_on_month'] && !isset($map['due_on'][$invoice['due_on_month']])) {
            $map['due_on'][$invoice['due_on_month']] = date("F Y", strtotime($invoice['due_on_month'] . '-1'));
          } // if
        } // foreach
      } // if

      // Sort maps
      uksort($map['issued_on'], 'strnatcasecmp');
      uksort($map['due_on'], 'strnatcasecmp');

      $map['issued_on'] = array_reverse($map['issued_on'], true);

      /**
       * makes more sense to have "soon due on" upcoming invoices at the top of the list
       * @since Feb 11th, 2013
       */
      // $map['due_on'] = array_reverse($map['due_on'], true);

      return $map;
    } // getIssuedAndDueDatesMap


    /**
     * Return array of issued on month
     *
     * @param Company $company
     * @return array
     */
    static function mapIssuedOnMonth(Company $company = null) {
      if($company instanceof Company) {
        $invoices = self::find(array(
          'conditions' => array('company_id = ? AND date_field_2 IS NOT NULL AND type = ?', $company->getId(), 'Invoice'),
          'order' => 'date_field_2 desc'
        ));
      } else {
        $invoices = self::find(array(
          'conditions' => array('date_field_2 IS NOT NULL AND type = ?', 'Invoice'),
          'order' => 'date_field_2 desc'
        ));
      }//if
      $map = array();
      if(is_foreachable($invoices)) {
        foreach($invoices as $invoice) {
          if($invoice->getStatus() > 0) {
            $map[$invoice->getIssuedOnMonth()] = date('F, Y', strtotime($invoice->getIssuedOn())+get_user_gmt_offset());
          }//if
        }//foreach
      }//if
      return $map;
    }//mapIssuedOnMonth


    /**
     * Return array of due on month
     *
     * @param Company $company
     * @return array
     */
    static function mapDueOnMonth(Company $company = null) {
      if($company instanceof Company) {
        $invoices = Invoices::find(array(
          'conditions' => array('company_id = ? AND type = ?', $company->getId(), 'Invoice'),
          'order' => 'date_field_1 desc'
        ));
      } else {
        $invoices = Invoices::find(array(
          'conditions' => "type = 'Invoice'",
          'order' => 'date_field_1 desc'
        ));
      }
      $map = array();
      if(is_foreachable($invoices)) {
        foreach($invoices as $invoice) {
          if($invoice->getStatus() > 0) {
            $map[$invoice->getDueOnMonth()] = date('F, Y', strtotime($invoice->getDueOn())+get_user_gmt_offset());
          }//if
        }//foreach
      }//if
      return $map;
    } // mapIssuedOnMonth

    /**
     * Finds the invoices for the objects list - general rule is to resemble the ApplicationObject::describe result
     *
     * @param User $user
     * @param Company $company
     * @param int $state
     * @return array
     */
    static function findForObjectsList(User $user, $company = null, $state = STATE_VISIBLE) {
      $result = array();

      $companies_map = Companies::getIdNameMap(null, STATE_ARCHIVED);
      $currencies_map = Currencies::getIdDetailsMap();

      $today = new DateValue(time() + get_user_gmt_offset());

      if ($company instanceof Company) {
        $invoices_table = TABLE_PREFIX . 'invoice_objects';
        $invoices = DB::execute("SELECT $invoices_table.id,$invoices_table.state, $invoices_table.company_id, $invoices_table.date_field_2 as issued_on, $invoices_table.date_field_1 as due_on, $invoices_table.varchar_field_1 as name, status, $invoices_table.created_on, $invoices_table.total, $invoices_table.currency_id FROM $invoices_table WHERE $invoices_table.company_id = ? AND status IN (?) AND $invoices_table.type = ? AND $invoices_table.state = ? ORDER BY $invoices_table.created_on DESC", $company->getId(), self::getVisibleStatuses($user), 'Invoice', $state);
        $view_invoice_url_template = Router::assemble('people_company_invoice', array('company_id' => '--COMPANYID--', 'invoice_id' => '--INVOICEID--'));
      } else {
        $invoices_table = TABLE_PREFIX . 'invoice_objects';
        $companies_table = TABLE_PREFIX . 'companies';
        $invoices = DB::execute("SELECT $invoices_table.id, $invoices_table.state, $invoices_table.company_id, $invoices_table.date_field_2 as issued_on, $invoices_table.date_field_1 as due_on, $companies_table.name AS company_name, $invoices_table.varchar_field_1 as name, status, $invoices_table.created_on, $invoices_table.total, $invoices_table.currency_id FROM $invoices_table,$companies_table WHERE $invoices_table.company_id = $companies_table.id AND $invoices_table.status IN (?) AND $invoices_table.type = ? AND $invoices_table.state = ? ORDER BY $invoices_table.created_on DESC", self::getVisibleStatuses($user), 'Invoice', $state);
        $view_invoice_url_template = Router::assemble('invoice', array('invoice_id' => '--TEMPLATE--'));
      } // if

      if ($invoices) {
        $invoices->setCasting(array(
          'due_on' => DBResult::CAST_DATE,
          'issued_on' => DBResult::CAST_DATE,
          'created_on' => DBResult::CAST_DATETIME,
        ));

        foreach ($invoices as $invoice) {
          $is_overdue = $invoice['status'] == INVOICE_STATUS_ISSUED && $invoice['due_on'] instanceof DateValue && $invoice['due_on']->toMySql() < $today->toMySql();

          $result[] = array(
            'id' => $invoice['id'],
            'name' => $invoice['name'] ? $invoice['name'] : lang('Draft #:invoice_num', array('invoice_num' => $invoice['id'])),
            'long_name' => $invoice['name'] ? lang('Invoice :name', array('name' => $invoice['name'])) : lang('Invoice Draft #:invoice_num', array('invoice_num' => $invoice['id'])),
            'client_id' => $invoice['company_id'],
            'client_name' => array_var($companies_map, $invoice['company_id'], lang('Unknown')),
            'currency' => array_var($currencies_map, $invoice['currency_id'], array()),
            'total' => $invoice['total'],
            'issued_on_month' => $invoice['issued_on'] instanceof DateValue ? $invoice['issued_on']->getYear() . '-' . $invoice['issued_on']->getMonth() : '',
            'due_on_month' => $invoice['due_on'] instanceof DateValue ? $invoice['due_on']->getYear() . '-' . $invoice['due_on']->getMonth() : '',
            'status' => $invoice['status'],
            'is_overdue' => $is_overdue,
            'state' => $invoice['state'],
            'permalink' => $company instanceof Company
              ? str_replace(array('--COMPANYID--', '--INVOICEID--'), array($company->getId(), $invoice['id']), $view_invoice_url_template)
              : str_replace('--TEMPLATE--', $invoice['id'], $view_invoice_url_template)
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Return invoices for API
     *
     * @param User $user
     * @return Invoice[]
     */
    static function findForApi($user) {
      return Invoices::find(array(
        'conditions' => array('type = ? AND state >= ? AND status > ?', 'Invoice', STATE_VISIBLE, INVOICE_STATUS_DRAFT),
        'order_by' => 'created_on',
      ));
    } // findForApi

    /**
     * Finds the invoices for the phone list view
     *
     * @param User $user
     * @param Company $company
     * @return array
     */
    static function findForPhoneList(User $user, $company = null) {
      $invoices_table = TABLE_PREFIX . 'invoice_objects';
      $companies_table = TABLE_PREFIX . 'companies';

      if($company instanceof Company) {
        $invoices = DB::execute("SELECT $invoices_table.id, $invoices_table.company_id, $invoices_table.varchar_field_1 as name, status FROM $invoices_table WHERE $invoices_table.company_id = ? AND $invoices_table.status IN (?) AND $invoices_table.type = ? ORDER BY $invoices_table.status, $invoices_table.id", $company->getId(), self::getVisibleStatuses($user), 'Invoice');
        $view_invoice_url_template = Router::assemble('people_company_invoice', array('company_id' => '--COMPANYID--', 'invoice_id' => '--INVOICEID--'));
      } else {
        $invoices = DB::execute("SELECT $invoices_table.id, $invoices_table.company_id, $invoices_table.varchar_field_1 as name, status FROM $invoices_table, $companies_table WHERE $invoices_table.company_id = $companies_table.id AND $invoices_table.status IN (?) AND $invoices_table.type = ? ORDER BY $invoices_table.status, $invoices_table.id", self::getVisibleStatuses($user), 'Invoice');
        $view_invoice_url_template = Router::assemble('invoice', array('invoice_id' => '--TEMPLATE--'));
      } // if

      $result = array();

      if(is_foreachable($invoices)) {
        foreach($invoices as $invoice) {
          $result[$invoice['status']][] = array(
            'name' => $invoice['name'] ? $invoice['name'] : lang('Draft #:invoice_num', array('invoice_num' => $invoice['id'])),
            'permalink' => $company instanceof Company ? str_replace(array('--COMPANYID--', '--INVOICEID--'), array($company->getId(), $invoice['id']), $view_invoice_url_template) : str_replace('--TEMPLATE--', $invoice['id'], $view_invoice_url_template)
          );
        } // foreach
      } // if

      return $result;
    } // findForPhoneList

    /**
     * Find invoices for printing by grouping and filtering criteria
     *
     * @param User $user
     * @param Company $company
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    static public function findForPrint(User $user, $company = null, $group_by = null, $filter_by = null, $state = STATE_VISIBLE) {
      if (!in_array($group_by, array('client_id', 'status'))) {
        $group_by = null;
      } // if

      if($group_by == 'client_id') {
        $group_by = 'company_id';
      } // if

      $conditions = array("type = 'Invoice'");

      // filter by completion status
      $filter_is_completed = array_var($filter_by, 'status', null);
      if ($filter_is_completed === '0' && $user->isFinancialManager()) {
        $conditions[] = DB::prepare('(status=?)', INVOICE_STATUS_DRAFT);
      } else if ($filter_is_completed === '1') {
        $conditions[] = DB::prepare('(status=?)', INVOICE_STATUS_ISSUED);
      } else if ($filter_is_completed === '2') {
        $conditions[] = DB::prepare('(status=?)', INVOICE_STATUS_PAID);
      } else if ($filter_is_completed === '3') {
        $conditions[] = DB::prepare('(status=?)', INVOICE_STATUS_CANCELED);
      } else {
        $conditions[] = DB::prepare('(status IN (?))', self::getVisibleStatuses($user));
      } // if

      $conditions[] = DB::prepare('state = ?', $state);

      if ($company instanceof Company) {
        $conditions[] = DB::prepare('company_id = ?', $company->getId());
      } // if

      // do find invoices
      $invoices = Invoices::find(array(
        'conditions' => implode(' AND ', $conditions),
        'order' => $group_by ? $group_by : 'id DESC'
      ));

      return $invoices;
    } // findForPrint

    /**
     * Get Settings for invoice form
     *
     * @param Invoice $invoice
     * @return array
     */
    static function getSettingsForInvoiceForm(Invoice $invoice) {
      $result = parent::getSettingsForInvoiceForm($invoice);
      $result['add_invoice_url'] = Router::assemble('invoices_add');
      return $result;
    } // getSettingsForInvoiceForm

  }