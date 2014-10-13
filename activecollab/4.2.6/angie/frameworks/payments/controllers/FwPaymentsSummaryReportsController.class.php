<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', SYSTEM_MODULE);
  

  /**
   * Payments summary reports controller implementation
   *
   * @package angie.frameworks.payments
   * @subpackage controllers
   */
  abstract class FwPaymentsSummaryReportsController extends DataFiltersController {
    
     /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $this->response->assign(array(
        'currencies' => Currencies::getIdDetailsMap()
      ));
    } // __construct
    
    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'PaymentSummaryReport';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'payments_summary_report_id';
    } // getFilterIdVariableName
    
    /**
     * Show payment report form and options
     */
    function index() {
      parent::index();
      
      $this->response->assign(array(
        'users' => Users::getForSelect($this->logged_user),
        'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
        'payment_statuses' => Payments::getStatuses(),
      ));
    } // index

    /**
     * Run payment filter
     */
    function run() {
      parent::run();

    }//run
    
  }