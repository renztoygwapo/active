<?php

  /**
   * Reports framework initialization file
   * 
   * @package angie.frameworks.reports
   */

  const REPORTS_FRAMEWORK = 'reports';
  const REPORTS_FRAMEWORK_PATH = __DIR__;
  
  // Name of the module that provides interface to this framework
  defined('REPORTS_FRAMEWORK_INJECT_INTO') or define('REPORTS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwReportsPanel' => REPORTS_FRAMEWORK_PATH . '/models/FwReportsPanel.class.php',  
    'IReportsPanelRow' => REPORTS_FRAMEWORK_PATH . '/models/IReportsPanelRow.class.php',  
    'ReportsPanelRow' => REPORTS_FRAMEWORK_PATH . '/models/ReportsPanelRow.class.php',

    // Data filters
    'FwDataFilter' => REPORTS_FRAMEWORK_PATH . '/models/data_filters/FwDataFilter.class.php',
    'FwDataFilters' => REPORTS_FRAMEWORK_PATH . '/models/data_filters/FwDataFilters.class.php',

    'DataFilterConditionsError' => REPORTS_FRAMEWORK_PATH . '/models/data_filters/DataFilterConditionsError.class.php',
    'DataFilterExportError' => REPORTS_FRAMEWORK_PATH . '/models/data_filters/DataFilterExportError.class.php',
  ));