<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Tracking reports controller implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class TrackingReportsController extends DataFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'TrackingReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'tracking_report_id';
    } // getFilterIdVariableName
    
    /**
     * Active tracking report
     *
     * @var TrackingReport
     */
    protected $active_tracking_report;
    
    /**
     * Invoice controller delegate
     * 
     * @var InvoiceBasedOnController
     */
    protected $invoice_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if(AngieApplication::isModuleLoaded('invoicing') && $this->getControllerName() == 'tracking_reports') {
        $this->invoice_delegate = $this->__delegate('invoice_based_on', INVOICING_MODULE, 'tracking_report');
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(AngieApplication::isModuleLoaded('invoicing') && $this->invoice_delegate instanceof InvoiceBasedOnController) {
        $this->invoice_delegate->__setProperties(array(
          'active_object' => &$this->active_data_filter
        ));
      } // if
    } // __construct
    
    /**
     * Show tracking report form and options
     */
    function index() {
      parent::index();

      $report = new TrackingReport();

      $users = Users::getForSelect($this->logged_user);
      if(is_foreachable($users)) {
        foreach($users as $k => $v) {
          $users[$k] = JSON::valueToMap($v);
        } // foreach
      } // if

      $this->response->assign(array(
        'users' => $users,
        'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
        'projects' => Projects::getIdNameMap($this->logged_user, STATE_ARCHIVED, null, null, true),
        'active_projects' => Projects::getIdNameMap($this->logged_user, STATE_VISIBLE, null, null, true), // We need this so we can group projects in the report
        'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
        'job_types' => JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE),
        'expense_categories' => ExpenseCategories::getIdNameMap(),
        'currencies' => Currencies::getIdDetailsMap(),
        'change_status_url' => Router::assemble('tracking_reports_change_status')
      ));

      if(AngieApplication::isModuleLoaded('invoicing') && Invoices::canAdd($this->logged_user)) {
        $this->response->assign('invoice_based_on_url', $report->invoice()->getUrl());
      } else {
        $this->response->assign('invoice_based_on_url', false);
      } // if
    } // index

    /**
     * Change records status from tracking report
     */
    function change_records_status() {
      $data = $this->request->post('data');
      try {
        $new_status = (integer) $data['new_status'];
        $time_record_ids = $data['time_records'];
        $expense_ids = $data['expenses'];

        if(is_foreachable($time_record_ids)) {
          TimeRecords::changeBilableStatusByIds($time_record_ids, $new_status);
        } //if
        if(is_foreachable($expense_ids)) {
          Expenses::changeBilableStatusByIds($expense_ids, $new_status);
        } //if

        $this->response->respondWithData(array('time_records' => $time_record_ids, 'expenses' => $expense_ids, 'new_status' => $new_status));
      } catch (Error $e) {
        $this->response->exception($e);
      } //try

    } //change_records_status

  }