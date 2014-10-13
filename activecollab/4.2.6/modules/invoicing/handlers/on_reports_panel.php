<?php

  /**
   * on_reports_panel event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_reports_panel event
   * 
   * @param ReportsPanel $panel
   * @param User $user
   */
  function invoicing_handle_on_reports_panel(ReportsPanel &$panel, User &$user) {
    if($user->isFinancialManager()) {
      if(!$panel->rowExists('finances')) {
        $panel->defineRow('finances', new ReportsPanelRow(lang('Finances')));
      } // if

      $panel->addTo('finances', 'detailed_invoices', lang('Invoices'), Router::assemble('detailed_invoices_filters'), AngieApplication::getImageUrl('common/filter.png', REPORTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT), array(
        'begin_with' => true,
      ));
      //$panel->addTo('finances', 'summarized_invoices', lang('Invoiced vs Due Amount'), Router::assemble('summarized_invoices_filters'), AngieApplication::getImageUrl('common/filter.png', REPORTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
    } // if
  } // invoicing_handle_on_reports_panel