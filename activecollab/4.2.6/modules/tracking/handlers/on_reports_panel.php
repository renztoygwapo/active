<?php

  /**
   * on_reports_panel event handler
   * 
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_reports_panel event
   * 
   * @param ReportsPanel $panel
   * @param User $user
   */
  function tracking_handle_on_reports_panel(ReportsPanel &$panel, User &$user) {
    if($user->isProjectManager() || $user->isFinancialManager()) {
      $panel->defineRow('tracking', new ReportsPanelRow(lang('Time and Expenses')));
      
      if($user->canSeeProjectBudgets()) {
        $panel->addTo('tracking', 'budget_vs_cost', lang('Budget vs Cost'), Router::assemble('budget_vs_cost_report'), AngieApplication::getImageUrl('reports/budget-vs-cost.png', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT));
      } // if

      $panel->addTo('tracking', 'estimated_vs_tracked_time', lang('Time Estimates'), Router::assemble('estiamted_vs_tracked_time_report'), AngieApplication::getImageUrl('reports/estimates-vs-tracked-time.png', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      $panel->addTo('tracking', 'custom', lang('Time and Expenses'), Router::assemble('tracking_reports'), AngieApplication::getImageUrl('common/filter.png', REPORTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
    } // if
  } // tracking_handle_on_reports_panel