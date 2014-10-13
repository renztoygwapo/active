<?php

  /**
   * on_reports_panel event handler
   * 
   * @package angie.frameworks.payments
   * @subpackage handlers
   */

  /**
   * Handle on_reports_panel event
   * 
   * @param ReportsPanel $panel
   * @param User $user
   */
  function payments_handle_on_reports_panel(ReportsPanel &$panel, User &$user) {
    if($user->isFinancialManager()) {
      if(!$panel->rowExists('finances')) {
        $panel->defineRow('finances', new ReportsPanelRow(lang('Finances')));
      } // if
     
      $panel->addTo('finances', 'payments_reports', lang('Payments Report'), Router::assemble('payments_reports'), AngieApplication::getImageUrl('reports/payments_report.png', PAYMENTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
      $panel->addTo('finances', 'payments_summary_reports', lang('Payments Summary'), Router::assemble('payments_summary_reports'), AngieApplication::getImageUrl('reports/payments_summary_report.png', PAYMENTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
    } // if
  } // payments_handle_on_reports_panel